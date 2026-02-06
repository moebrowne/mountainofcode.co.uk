$fn=75;

$width=14;
$depth=14;
$height=16.6;

module body() {
    translate([-($width/2),-($depth/2),0]) {
        cube([$width,$depth,$height]);
    }
}

module holes() {
    $diameter=8.1;
    $radius=$diameter/2;

    module lowerHole() {
        $offset=5;
        
        translate([-$width,0,$offset+$radius]) {
            rotate([0,90,0]) {
                    cylinder(d=8.1, h=$width*2);
            }
        }
    }

    module upperHole() {
        $offset=12.5;
        
        translate([0,$depth,$offset+$radius]) {
            rotate([90,90,0]) {
                    cylinder(d=$diameter, h=$depth*2);
            }
        }
    }
    
    module gap() {
        translate([0,-1,15]) {
            rotate([5,0,0]) {
                cube([$width*2,5,10], center=true);
            }
        }
        translate([0,1,15]) {
            rotate([-5,0,0]) {
                cube([$width*2,5,10], center=true);
            }
        }
    }
    
    union() {
        lowerHole();
        upperHole();
        gap();
    }
}


intersection() {
    difference() {
        body();
        holes();
    }
    cylinder(h=$height, d=18.5);
    translate([0,0,21]) {
        sphere(d=45);
    }
}