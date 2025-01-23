## Information
I decided to take a look on Laravel during this assigment, as I never worked with it before.
Also, I never used Api Platform with the Laravel as well (as I know now documentation is not good in comparison with the Symfony).
It took more time than I've expected.


## To run code inside docker container:
```shell
docker run -p 8080:8080 palamar/parking:latest
```
after that open in browser https://127.0.0.1:8000

## Income per time period for the parking and fee:
```sql
SELECT 'payments', sum(amount) FROM payments p WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01"
UNION
SELECT 'fees', sum(amount) FROM fee_payments fp WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01";
```

## Vehicles tested by operator:
```sql
```

## Vehicles per zone per time period:
```sql
SELECT z.code, sum(vehicles) as vehicle FROM (
SELECT zone_id, count(vehicle_id) as vehicles FROM payments p WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01" GROUP BY zone_id
UNION
SELECT zone_id, count(vehicle_id) as vehicles FROM fees f  WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01" GROUP BY zone_id
) a INNER JOIN zones z ON a.zone_id = z.id
```

### Repeated users of the parked system by vehicle plate:
```sql
SELECT plate, COUNT(vehicle_id) as used_times FROM payments p
INNER JOIN vehicles v ON v.id=p.vehicle_id
WHERE p.created_at >= "2025-01-01" AND p.created_at < "2025-02-01"
GROUP BY plate
HAVING used_times > 2
ORDER BY used_times DESC
```

# Repeated vehicles that don't pay for the parking:
```sql
SELECT plate, COUNT(vehicle_id) as used_times FROM fees p
INNER JOIN vehicles v ON v.id=p.vehicle_id
WHERE p.created_at >= "2025-01-01" AND p.created_at < "2025-02-01"
GROUP BY plate
HAVING used_times > 0
ORDER BY used_times DESC
```
