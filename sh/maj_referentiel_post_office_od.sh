pg_dump -t od_postes_dept_1204 --inserts -f data/to_load/insert_od_postes_dept.txt osm
echo 'TRUNCATE TABLE od_postes_dept;' > data/to_load/insert_od_postes_dept.sql
cat data/to_load/insert_od_postes_dept.txt|grep 'INSERT INTO' |sed s/dept_1204/dept/g >> data/to_load/insert_od_postes_dept.sql

