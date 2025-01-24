## Information
I decided to take a look on Laravel during this assigment, as I never worked with it before.
Also, I never used Api Platform with the Laravel as well (as I know now documentation is not good in comparison with the Symfony).  
It took more time than I've expected.  
As database was used SQlite, it's not the best choice as Laravel by default ignore all foreign keys for this type of DB.  
But it allowed to pack DB inside a code container without need of docker-compose.yaml file to coordinate containers.

## To run code inside docker container (image already uploaded to the Docker Hub):
```shell
docker run -p 8080:8080 palamar/parking:latest
```
or you can use docker-compose.yaml file
```shell
docker compose up
```
after that open it in browser https://127.0.0.1:8080

## How to build and run locally (if you don't like version from the Docker Hub):
> [!IMPORTANT]
> PHP 8.3 and composer must be installed and preconfigured locally to do this.

```bash
# be in the branch directory and run commands in a shell
./build.sh # get composer packages via composer and configure and seed DB with conf. info.
docker compose up
```

## Notes regarding code
There are not such sings as DTO for the Response, that allows much better control of the types and output formatting.  
But usage of the dynamic arrays just faster to code.  
Some checks may be organised as a list of preconfigured separate validation classes instead list of ifs.  
Also there are missed unit/integration tests.




## Database schema (more or less actual)
![<a href="https://raw.githubusercontent.com/palamar/parking/refs/heads/main/QuickDBD.svg" />](QuickDBD.svg)

[Full size image](https://raw.githubusercontent.com/palamar/parking/refs/heads/main/QuickDBD.svg)

## Income per time period for the parking and fee:
```sql
SELECT 'payments', sum(amount) FROM payments p WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01"
UNION
SELECT 'fees', sum(amount) FROM fee_payments fp WHERE created_at >= "2025-01-01" AND created_at < "2025-02-01";
```

## Vehicles tested by operator:
```sql
SELECT CONCAT_WS(" ", o.name, o.surname) as fullname, COUNT(rl."action") as scans
FROM request_logs rl INNER JOIN operators o ON rl.operator_id = o.id 
WHERE rl.created_at >= "2025-01-01" AND rl.created_at < "2025-02-01" AND rl."action" = "scan"
GROUP BY rl.operator_id 
ORDER BY scans DESC
LIMIT 10
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
