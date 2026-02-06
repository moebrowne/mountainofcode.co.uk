hull() {
    cube([2.5, 200, 6]);

    translate([0,110,40]) rotate([0,90,0]) cylinder(d=40, h=1);
}

!difference() {
    cube([146,146,4]);
    
    translate([66.5,0,0]){
        translate([1,2,0]) cube([2.0, 142, 6]);
        translate([5,2,0]) cube([2.5, 142, 6]);
        translate([9.5,2,0]) cube([3, 142, 6]);
    }
    
    translate([2,5,0]) {
        for(i=[0:18]) {
            translate([0,7.5*i,0]) cube([63.5, 2.0, 6]);
        }
    }
    
    translate([146-1-64,2,0]) {
        for(i=[0:20]) {
            translate([0,10*i,0]) cube([63, 2.0, 6]);
        }
    }
   
}
