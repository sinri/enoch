# Compare the two methods of PHP routing at thousand level
 
1. REGEX faster for registering but slower for seeking.
1. TREE faster for seeking but slower for registering.

So:

The Tree Router would be kept in this commit but would not be accepted as a normal version.

## Benchmark data

Test data with N paths, each of them has from zero component to ten components.
Each component might be 50% as fixed string, 30% as variable string, and the left 20% as optional variable string.

----

```bash
SinriMac:~/Codes/Leqee/fundament/enoch Sinri$ php test/routing/benchmark/route_test.php
TYPE REGEX
COUNT ROUTE PATHS: 1001
REGISTER SPENT 0.028478145599365 s
PATHS: 1001
REGISTER AVG: 2.8449695903462E-5 s
PROCESS SPENT 0.70773792266846 s
CASES: 1001
PROCESS AVG: 0.00070703089177668 s
TYPE TREE
COUNT ROUTE PATHS: 1001
REGISTER SPENT 0.14169907569885 s
PATHS: 1001
REGISTER AVG: 0.00014155751818067 s
PROCESS SPENT 0.040383100509644 s
CASES: 1001
PROCESS AVG: 4.0342757751892E-5 s
 ```

----

```bash
SinriMac:~/Codes/Leqee/fundament/enoch/test/routing/benchmark Sinri$ php route_test.php
TYPE REGEX
COUNT ROUTE PATHS: 10000
REGISTER SPENT 1.0323588848114 s
PATHS: 10000
REGISTER AVG: 0.00010323588848114 s
PROCESS SPENT 53.614212989807 s
CASES: 10000
PROCESS AVG: 0.0053614212989807 s
PROCESS STAT: {"done":10000,"fail":0}
TYPE TREE
COUNT ROUTE PATHS: 10000
REGISTER SPENT 4.0980060100555 s
PATHS: 10000
REGISTER AVG: 0.00040980060100555 s
PROCESS SPENT 0.67086100578308 s
CASES: 10000
PROCESS AVG: 6.7086100578308E-5 s
PROCESS STAT: {"done":10000,"fail":0}
```
 