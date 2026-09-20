# ESP Serial Connection Timeout

#ESP

Typically, the first few lines of my ESP projects look like this:

```cpp
void setup() {
  Serial.begin(115200);
  delay(2000);
  Serial.println("Awake");
  
  //...
}
```

The 2000 ms delay is to allow a PC to establish a serial connection. Without this messages get missed and debugging
becomes very frustrating.

Most of my ESP projects, however, are low-energy and battery-powered, designed to use as little energy as possible.
Burning 2 s every boot is a pure waste of energy.

The alternative is to add a connection check and timeout:

```cpp
void setup() {
  Serial.begin(115200);
  
  uint32_t serialWaitStart = millis();
  while (!Serial && (millis() - serialWaitStart) < 2000) {
    delay(1);
  }

  Serial.println("Awake");
  
  //...
}
```

This will wait for the Serial connection to fail or for 2 seconds to pass, whichever comes first.

If a Serial connection is not present, then all `{cpp}Serial.println()` calls are noops.

