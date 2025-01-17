# Highlighting OpenSCAD Code

#OpenSCAD
#Tempest Highlight

In my [last post on OpenSCAD](/3d-modeling-for-developers) I included some example code but the [tempest/highlight](https://tempestphp.com/docs/highlight/getting-started/)
package I use for code highlighting didn't support OpenSCAD code. I wanted to fix that.

The below code was used as a kind of smoke test, it mostly consists of snippets from the
[official cheatsheet](https://openscad.org/cheatsheet/index.html).

```openscad
cube([20,20,10]);
cube([20,PI,10]);
cube([variable_name,variable,$variable]);

translate([0,0,10]) cylinder(d=20, h=10);
translate([0,0,-10]) cylinder(d=20, h=10);

translate([0,0,10]) {
    cylinder(d=20, h=10);
}

import("file/path/open.scad");

module Thing(param1, param2) {
    sphere(r=10);
}

var=10;
$specialVar=20;

//comment

translate([PI,0,0]) cube([1,2,3]);

for(i = [0:1:10]) {

}

for(i = [0:10]) {

}

function parabola(f,x) = ( 1/(4*f) ) * x*x;

module plot(x,y) {
translate([x,y])
  circle(1,$fn=12);
} 

module plotParabola(f,wide,steps=1) {
  function y(x) = parabola(f,x);
  
  
  xAxis=[-wide/2:steps:wide/2];
  for(x=xAxis) 
    plot(x, y(x));
}
color("red")  plotParabola(10, 100, 5);
color("blue") plotParabola(4,  60,  2);

scale([1/100, 1/100, 1/100]) circle(200); // create a high resolution circle with a radius of 2.
circle(2, $fn=50);                        // Another way.

circle(10);
circle(r=10);
circle(d=20);
circle(d=2+9*2);

include <thing.scad>;
use <thing.scad>;

test ? true : 'false'

// generate a list with all values defined by a range
list1 = [ for (i = [0 : 2 : 10]) i ];
echo(list1); // ECHO: [0, 2, 4, 6, 8, 10]

eps = 0.01;
translate([eps, 60, 0])
   rotate_extrude(angle=270, convexity=10)
       translate([40, 0]) circle(10);
rotate_extrude(angle=90, convexity=10)
   translate([20, 0]) circle(10);
translate([20, eps, 0])
   rotate([90, 0, 0]) cylinder(r=10, h=80+eps);
   
linear_extrude(height = fanwidth, center = true, convexity = 10)
   import(file = "example009.dxf", layer = "fan_top");
   
square(size = [x, y], center = false);
square(size =  x    , center = true);

a=[for(i=[0:10])i%2];
echo(a);//ECHO: [0, 1, 0, 1, 0, 1, 0, 1, 0, 1, 0]

for(i=[0:10]) translate([i,i%2?0:5]) cube(1); // move every even up

polyhedron( points = [ [X0, Y0, Z0], [X1, Y1, Z1] ], triangles = [ [P0, P1, P2] ], convexity = N);   // before 2014.03
polyhedron( points = [ [X0, Y0, Z0], [X1, Y1, Z1] ], faces = [ [P0, P1, P2, P3 ] ], convexity = N);   // 2014.03 & later

module myModification() { rotate([0,45,0]) children(); } 

myModification()                 // The modification
{                                // Begin focus
    cylinder(10,4,4);            // First child
    cube([20,2,2], true);        // Second child
}                                // End focus

for(i=[0:36]) {
    for(j=[0:36]) {
        color( [0.5+sin(10*i)/2, 0.5+sin(10*j)/2, 0.5+sin(10*(i+j))/2] )
        translate( [i, j, 0] )
        cube( size = [1, 1, 11+10*cos(10*i)*sin(10*j)] );
    }
}
```
