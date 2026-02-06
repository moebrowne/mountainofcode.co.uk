$fn=80;

module backArea() {
    translate([4.5,4.5,0]) cube([80,48,2]);
}

module blank() {
    scale(25.4) rotate([90,0,0]) import("reference/thingiverse-2810500.stl"); // https://www.thingiverse.com/thing:2810500
    translate([2,5,0]) cube([85,50,1]);
    translate([91,20,0]) cube([4,18,1]);
}

module blankBottom() {
    intersection() {
        blank();
        cube([98,57.2,1]);
    }
}

module blankTop() {
    intersection() {
        blank();
        translate([0,0,1]) cube([98,57.2,3]);
    }
}

module extendedBlank() {
    blankBottom();
    
    translate([0,0,1]) {
        linear_extrude(0.5) projection() blankBottom();
    }
    
    translate([0,0,0.5]) {
        blankTop();
        clipRenforcement();
    }
}


module clipRenforcement() {
    translate([92.5,23.48,0.5]) {
        hull() {
            cylinder(d=11, h=1.5);
            translate([0,10.2,0]) cylinder(d=11, h=1.5);
        }
    }
}

module clipHole() {
    translate([92.65,23.48,0]) {
        hull() {
            cylinder(d=4.5, h=20);
            translate([0,10.2,0]) cylinder(d=4.5, h=20);
        }
    }
}


module hexagon(diameter) {
    rotate([0,0,30]) cylinder(d=diameter,h=2, $fn=6);
}

module hexagons() {
    diameter=10;
    
    for(x = [1 : 1 : 8]) {
        for(y = [1 : 1 : 20]) {
            gap=0;
            offsetX = ((y/2) == round(y/2)) ? (diameter/2)-(gap/2):0;
            offsetY = 3;
            
            translate([((diameter-gap)*x)-offsetX,((diameter-gap-1)*y)-offsetY,0]) hexagon(diameter);
        }
    }
}

module triangle(size) {
    cylinder(d=size,h=2, $fn=3);
}

module triangles() {
    size=10;
    
    for(x = [1 : 1 : 8]) {
        for(y = [1 : 1 : 20]) {
            gap=5;
            offsetX = ((y/2) == round(y/2)) ? (size/2)-(gap/2):2;
            offsetY = 3;
            
            translate([((size-gap)*x)-offsetX,((size-gap-1)*y)-offsetY,0]) rotate([0,0,180*y]) triangle(size);
        }
    }
}

module star(size) {
    rotate([0,0,0]) cylinder(d=size,h=2, $fn=3);
    rotate([0,0,60]) cylinder(d=size,h=2, $fn=3);
}

module stars() {
    size=11;
    
    for(x = [1 : 1 : 8]) {
        for(y = [1 : 1 : 20]) {
            gap=-2;
            offsetX = ((y/2) == round(y/2)) ? (size/2)-(gap/2):0;
            offsetY = 3;
            
            translate([((size-gap)*x)-offsetX,((size-gap-1)*y)-offsetY,0]) rotate([0,0,180*y]) star(size);
        }
    }
}

module wineGlass() {
    linear_extrude(height=2) projection(cut=true) {
        scale(0.03) surface(file="reference/wine-glass.png", center=true, invert=true, convexity=5);
    }
}

module wineGlasses() {
    for(x = [1 : 1 : 5]) {
        for(y = [1 : 1 : 5]) {
            gapX=17;
            gapY=9;
            offsetX = ((y/2) == round(y/2)) ? (gapX/2):0;
            offsetY = 3;
            
            translate([(gapX*x)-offsetX,(gapY*y)-offsetY,0]) rotate([0,0,-90]) wineGlass();
        }
    }
    
}

module badgeHolder() {
    difference() {
        union() {
            extendedBlank();

            translate([0,0,0.5]) {
                clipRenforcement();
            }
        }
        clipHole();
    }
}

module holderWithHexagons() {
    difference() {
        badgeHolder();
        union() {
            intersection() {
                backArea();
                hexagons();
            }
        }
    }
}

module holderWithStars() {
    difference() {
        badgeHolder();
        union() {
            intersection() {
                backArea();
                translate([-1,2,0]) stars();
            }
        }
    }
}

module holderWithWineGlasses() {
    difference() {
        badgeHolder();
        translate([-8,4.5,0]) wineGlasses();
    }
}

holderWithHexagons();

//Part: holderWithHexagons();
//Part: holderWithStars();
//Part: holderWithWineGlasses();
