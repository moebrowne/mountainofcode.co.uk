# Efficient ESP32 WiFi Connection

#ESP
#WiFi

I have used ESPs for a bunch of different projects, but my favourites are the remote, low energy, battery-powered ones.
It usually amounts to putting the ESP into deep sleep for as long as possible, only briefly waking to take read a sensor
and transmit the data. Sometimes a small solar panel too.

I use WiFi to send the data. I know there are much lower power ways, but it's easy and built-in. Also, I like a
challenge 😁 WiFi is obviously where the majority of the energy is being spen t, so that is the place to target for
optimisations. There are 4 things which the ESP has to figure out before packets can be sent:

1. IP config (gateway, DHCP, subnet)
2. DNS server to use
3. Access point channel
4. Access point BSSID

I thought that the more of these I could provide up front as static values, the less time the ESP has to spend figuring
them out, right?

1. Setting the IP config upfront maybe helped a little bit, but I'm not convinced.
2. The DNS server made no detectable difference, probably because of remote caching.
3. The channel made no difference at all.
4. Setting the BSSID made it 10 times worse, which I don't understand at all...

These are the full results for an ESP32-C3, and the code used to get them:

```
Config                                           WiFi avg ms  TCP avg ms
IP:static Chan:fixed BSSID:auto DNS:default      21.7         29.4
IP:static Chan:auto BSSID:auto DNS:custom        23.3         29.2
IP:static Chan:auto BSSID:auto DNS:default       23.4         27.5
IP:dhcp Chan:fixed BSSID:auto DNS:default        34.1         21.5
IP:dhcp Chan:auto BSSID:auto DNS:default         35.2         21.1
IP:static Chan:fixed BSSID:auto DNS:custom       36.4         28.4
IP:dhcp Chan:auto BSSID:auto DNS:custom          36.6         23.2
IP:dhcp Chan:fixed BSSID:auto DNS:custom         41.1         21.8
IP:static Chan:fixed BSSID:fixed DNS:custom      289.2        27.4
IP:static Chan:fixed BSSID:fixed DNS:default     289.3        26.4
IP:static Chan:auto BSSID:fixed DNS:custom       289.8        29.7
IP:dhcp Chan:fixed BSSID:fixed DNS:custom        316.8        22.8
IP:dhcp Chan:auto BSSID:fixed DNS:custom         356.3        21.7
IP:static Chan:auto BSSID:fixed DNS:default      394.5        134.0
IP:dhcp Chan:fixed BSSID:fixed DNS:default       405.6        23.1
IP:dhcp Chan:auto BSSID:fixed DNS:default        551.1        21.3
```

<details>
<summary>Test code</summary>

```cpp
#include <WiFi.h>

const char* SSID = "";
const char* PASS = "";

uint8_t TARGET_BSSID[6];
int TARGET_CHANNEL = 0;

IPAddress STATIC_IP(192, 168, 1, 50);
IPAddress GATEWAY(192, 168, 1, 1);
IPAddress SUBNET(255, 255, 255, 0);
IPAddress DNS1(1, 1, 1, 1);
IPAddress DNS2(1, 0, 0, 1);

const int SAMPLES = 10;
const int NUM_CONFIGS = 16;
const IPAddress TARGET(1, 1, 1, 1);
const uint16_t PORT = 80;
const uint32_t CONNECT_TIMEOUT_MS = 5000;
const uint32_t WIFI_CONNECT_TIMEOUT_MS = 15000;

struct Result {
  char label[64];
  uint64_t wifiTotalMs;
  uint64_t tcpTotalMs;
  int count;
};

Result results[NUM_CONFIGS];

void halt(const char* msg) {
  Serial.print("FATAL: ");
  Serial.println(msg);
  while (true) delay(1000);
}

void discoverAP(const char* ssid) {
  Serial.println("Scanning for AP...");
  int n = WiFi.scanNetworks(false, true);
  if (n <= 0) halt("no networks found in scan");

  int bestIdx = -1, bestRSSI = -1000;
  for (int i = 0; i < n; i++) {
    if (strcmp(WiFi.SSID(i).c_str(), ssid) == 0 && WiFi.RSSI(i) > bestRSSI) {
      bestRSSI = WiFi.RSSI(i);
      bestIdx = i;
    }
  }
  if (bestIdx < 0) halt("target SSID not found in scan results");

  const uint8_t* bssidPtr = WiFi.BSSID(bestIdx);
  if (bssidPtr == nullptr) halt("BSSID pointer null - scan result corrupted");
  memcpy(TARGET_BSSID, bssidPtr, 6);
  TARGET_CHANNEL = WiFi.channel(bestIdx);
  WiFi.scanDelete();

  Serial.printf("Selected AP: BSSID=%s channel=%d RSSI=%d\n", WiFi.BSSIDstr(bestIdx).c_str(), TARGET_CHANNEL, bestRSSI);
}

// Full isolation between every single trial: driver stop/start clears
// PMK cache, BSSID/channel lock, and any lingering static-IP/DNS state.
// Costs more time per sample, but cost is identical for every trial,
// so relative comparisons between configs stay valid.
void resetRadio() {
  WiFi.disconnect(true, false); // drop connection, do NOT touch NVS
  WiFi.mode(WIFI_OFF);
  delay(100);
  WiFi.mode(WIFI_STA);
  delay(100);
}

void connectWiFi(bool useStatic, bool useChannel, bool useBSSID, bool useDNS, uint32_t &wifiMs, const char* label) {
  resetRadio();

  if (useStatic) {
    if (useDNS) WiFi.config(STATIC_IP, GATEWAY, SUBNET, DNS1, DNS2);
    else        WiFi.config(STATIC_IP, GATEWAY, SUBNET, GATEWAY); // explicit, not left to cache
  } else {
    if (useDNS) WiFi.config(INADDR_NONE, INADDR_NONE, INADDR_NONE, DNS1, DNS2); // DHCP IP, forced custom DNS
    else        WiFi.config((uint32_t)0, (uint32_t)0, (uint32_t)0);             // full DHCP, no override
  }

  uint32_t t0 = millis();
  int32_t channel = useChannel ? TARGET_CHANNEL : 0;
  uint8_t* bssid = useBSSID ? TARGET_BSSID : NULL;
  WiFi.begin(SSID, PASS, channel, bssid, true);

  while (WiFi.status() != WL_CONNECTED) {
    if (millis() - t0 > WIFI_CONNECT_TIMEOUT_MS) {
      char msg[96];
      snprintf(msg, sizeof(msg), "WiFi connect failed/timed out for config: %s", label);
      halt(msg);
    }
    delay(1);
  }
  wifiMs = millis() - t0;
}

uint32_t testTcpOnce(const char* label, int sampleNum) {
  WiFiClient client;
  uint32_t t0 = millis();
  bool connected = client.connect(TARGET, PORT, CONNECT_TIMEOUT_MS);
  uint32_t t1 = millis();
  if (!connected) {
    char msg[128];
    snprintf(msg, sizeof(msg), "TCP connect failed on sample %d for config: %s", sampleNum, label);
    halt(msg);
  }
  client.stop();
  return t1 - t0;
}

void buildLabel(int mask, char* out) {
  bool useStatic  = mask & 0x1, useChannel = mask & 0x2, useBSSID = mask & 0x4, useDNS = mask & 0x8;
  snprintf(out, 64, "IP:%s Chan:%s BSSID:%s DNS:%s",
           useStatic ? "static" : "dhcp", useChannel ? "fixed" : "auto",
           useBSSID ? "fixed" : "auto", useDNS ? "custom" : "default");
}

void setup() {
  Serial.begin(115200);
  delay(1000);
  randomSeed(esp_random());

  Serial.printf("=== ESP32-C3 single-AP config sweep (%d combos, %d samples each, isolated) ===\n", NUM_CONFIGS, SAMPLES);

  WiFi.persistent(false);
  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);
  discoverAP(SSID);

  for (int i = 0; i < NUM_CONFIGS; i++) buildLabel(i, results[i].label);

  // Flat list of (config, sample) pairs, shuffled once, so trial order
  // across the whole run is randomized rather than grouped/sequential.
  // Cancels out warm-radio / ordering bias across configs.
  int total = NUM_CONFIGS * SAMPLES;
  int order[total];
  for (int i = 0; i < total; i++) order[i] = i;
  for (int i = total - 1; i > 0; i--) {
    int j = random(i + 1);
    int tmp = order[i]; order[i] = order[j]; order[j] = tmp;
  }

  for (int k = 0; k < total; k++) {
    int mask = order[k] % NUM_CONFIGS;
    bool useStatic  = mask & 0x1, useChannel = mask & 0x2, useBSSID = mask & 0x4, useDNS = mask & 0x8;

    uint32_t wifiMs = 0;
    connectWiFi(useStatic, useChannel, useBSSID, useDNS, wifiMs, results[mask].label);
    uint32_t tcpMs = testTcpOnce(results[mask].label, results[mask].count + 1);

    results[mask].wifiTotalMs += wifiMs;
    results[mask].tcpTotalMs += tcpMs;
    results[mask].count++;

    Serial.printf("[%3d/%3d] %-64s wifi=%lu ms tcp=%lu ms\n", k + 1, total, results[mask].label, wifiMs, tcpMs);
  }

  Serial.println("\n=== Summary (sorted by avg WiFi connect time) ===");
  int idx[NUM_CONFIGS];
  for (int i = 0; i < NUM_CONFIGS; i++) idx[i] = i;
  for (int i = 0; i < NUM_CONFIGS - 1; i++)
    for (int j = i + 1; j < NUM_CONFIGS; j++)
      if (results[idx[i]].wifiTotalMs * results[idx[j]].count > results[idx[j]].wifiTotalMs * results[idx[i]].count) {
        int t = idx[i]; idx[i] = idx[j]; idx[j] = t;
      }

  Serial.println("Config                                                          | WiFi avg ms | TCP avg ms");
  for (int i = 0; i < NUM_CONFIGS; i++) {
    Result &r = results[idx[i]];
    Serial.printf("%-64s| %11.1f | %.1f\n", r.label, (float)r.wifiTotalMs / r.count, (float)r.tcpTotalMs / r.count);
  }
}

void loop() {}
```

</details>

What it boils down to is I use this code:

```cpp
#include <WiFi.h>
#include <esp_sleep.h>
#include <esp_wifi.h>
#include "driver/gpio.h"

const char* WIFI_SSID = "";
const char* WIFI_PASSWORD = "";

const uint64_t SLEEP_INTERVAL_US = 5ULL * 1000000ULL;
const uint32_t WIFI_CONNECT_TIMEOUT_MS = 3000;


void setup() {
  Serial.begin(115200);
  Serial.println("Awake");

  Serial.print("Connecting to WiFi");
  WiFi.persistent(false);
  WiFi.mode(WIFI_STA);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);

  uint32_t wifiConnectStart = millis(), lastDot = 0;
  while (WiFi.status() != WL_CONNECTED && (millis() - wifiConnectStart) < WIFI_CONNECT_TIMEOUT_MS) {
    if (millis() - lastDot >= 100) Serial.print("."), lastDot = millis();
    delay(1);
  }

  if (WiFi.status() != WL_CONNECTED) {
    Serial.println(" FAILED!");
  } else {
    Serial.println(" OK!");
    
    // Do something
  }

  WiFi.disconnect(true);
  WiFi.mode(WIFI_OFF);

  Serial.println("Zzzzz");
  Serial.flush();
  esp_deep_sleep(SLEEP_INTERVAL_US);
}

void loop() {}
```


