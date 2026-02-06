$fn=50;

$armWidth=7.25;
$armHeight=$armWidth;
$armInnerWidth=4.75;
$armInnerHeight=$armInnerWidth;
$armLength=33/2;
$armWallThickness=($armWidth-$armInnerWidth)/2;

    
module armInner() {
    translate([($armWidth/2),-($armWidth/2)+$armWallThickness,$armWallThickness]) {
        cube([$armLength,$armInnerWidth,$armInnerHeight]);
    }
}

module arm() {
    module armOuter() {
        translate([($armWidth/2),-$armWidth/2,0]) {
            cube([$armLength-($armWidth/2),$armWidth,$armHeight]);
        }
    }

    module armScrewsPost() {
        translate([29/2,0,$armHeight]) {
            cylinder(d=2.75, h=1.1);
        }
    }
    
    module strut() {
        translate([$armLength-2,0,0]) {
            rotate([0,0,45]) {
                cube([2,20,$armHeight]);
            }
        }
    }
    
    module armBody() {
        union() {
            armOuter();
            armScrewsPost();
            cylinder(h=$armHeight, r=$armWidth);
            strut();
        }
    }
   
    
    difference() {
        armBody();
        armScrewHole();
    }
    

}
    
module armScrewHole() {
    translate([29/2,0, $armHeight/2]) {
        cylinder(h=5,d=1.25);
    }
}


module arms() {
    arm();
    rotate([0,0,90]) arm();
    rotate([0,0,180]) arm();
    rotate([0,0,270]) arm();
}

module armInners() {
    armInner();
    rotate([0,0,90]) armInner();
    rotate([0,0,180]) armInner();
    rotate([0,0,270]) armInner();
}

difference() {
    arms();
    armInners();
}