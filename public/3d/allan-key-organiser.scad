$fs = 0.5;
// Millimetre sizes
//unit = 1;
//sizes = [1.5, 2, 2.5, 3, 4, 4.5, 5, 5.5, 6, 7, 8, 9, 10];

// Inch sizes
unit = 25.4;
sizes = [1/16, 5/64, 3/32, 7/64, 1/8, 9/64, 5/32, 3/16, 7/32, 1/4, 5/16, 3/8];


slack = 0.2; // Add this to the radius above and below the central gripping ring
cd_ratio = 1.13; // Grip diameter to nominal size ratio; 1.15 is exact.

spacing = 2.75; // Gap between keys
wall = 1.5; // Thickness of outer wall and gripping ring
height = 15; // Overall height
$e = 0.01; // Used to ensure that the result is manifold

// Recursively translate and draw a cylinder
module holes(index = 0, extra = 0, h = height) {
  if (index < len(sizes)) {
    radius = sizes[index] * cd_ratio * unit / 2;
    rotate(360 / 12)
      cylinder(r = radius + extra, h = h);
    translate([radius * 2 + spacing, 0, 0])
      holes(index = index + 1, extra = extra, h = h);
  }
}

difference() {
  // The outside case is just a hull around the whole lot
  translate([0, 0, -height / 2])
    hull() {
      holes(h = height, extra = wall);
    }

  // Tightly-fitting holes for keys
  translate([0, 0, -height / 2 - $e])
    holes(h = height + 2 * $e);

  // Widen above and below
  for (theta = [0, 180])
    rotate(theta, [1, 0, 0])
      translate([0, 0, wall / 2])
        holes(h = height, extra = slack);
}
