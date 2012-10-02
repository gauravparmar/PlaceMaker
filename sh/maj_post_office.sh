mkdir data/backups

mv data/downloaded/guadeloupe.osm.pbf data/backups/.
mv data/downloaded/guyane.osm.pbf data/backups/.
mv data/downloaded/martinique.osm.pbf data/backups/.
mv data/downloaded/mayotte.osm.pbf data/backups/.
mv data/downloaded/reunion.osm.pbf data/backups/.
#mv data/downloaded/spm.osm.pbf data/backups/.
mv data/downloaded/france.osm.pbf data/backups/.

curl -o data/downloaded/guadeloupe.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france/guadeloupe.osm.pbf
curl -o data/downloaded/guyane.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france/guyane.osm.pbf
curl -o data/downloaded/martinique.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france/martinique.osm.pbf
curl -o data/downloaded/mayotte.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france/mayotte.osm.pbf
curl -o data/downloaded/reunion.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france/reunion.osm.pbf
#curl -o data/downloaded/spm.osm.pbf -x 192.168.221.2:8080 http://download.openstreetmap.fr/extracts/saint_pierre_et_miquelon.osm.pbf

./osmosis-0.40.1/bin/osmosis --rb file=data/downloaded/guadeloupe.osm.pbf outPipe.0=d1 --rb file=data/downloaded/guyane.osm.pbf outPipe.0=d2 --merge inPipe.0=d1 inPipe.1=d2 --wb file=data/to_load/tmp_dom_1.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/downloaded/martinique.osm.pbf outPipe.0=d1 --rb file=data/downloaded/mayotte.osm.pbf outPipe.0=d2 --merge inPipe.0=d1 inPipe.1=d2 --wb file=data/to_load/tmp_dom_2.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/downloaded/reunion.osm.pbf outPipe.0=d1 --rb file=data/downloaded/spm.osm.pbf outPipe.0=d2 --merge inPipe.0=d1 inPipe.1=d2 --wb file=data/to_load/tmp_dom_3.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/to_load/tmp_dom_1.osm.pbf outPipe.0=d1 --rb file=data/to_load/tmp_dom_2.osm.pbf outPipe.0=d2 --merge inPipe.0=d1 inPipe.1=d2 --wb file=data/to_load/tmp_dom_4.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/to_load/tmp_dom_3.osm.pbf outPipe.0=d1 --rb file=data/to_load/tmp_dom_4.osm.pbf outPipe.0=d2 --merge inPipe.0=d1 inPipe.1=d2 --wb file=data/to_load/tous_doms.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/to_load/tous_doms.osm.pbf --tee 3 --tf accept-relations amenity=post_office --used-way idTrackerType=BitSet --used-node idTrackerType=BitSet --wb file=data/to_load/po_dom_rel.osm.pbf --tf reject-relations --tf accept-ways amenity=post_office --used-node --wb file=data/to_load/po_dom_way.osm.pbf --tf accept-nodes amenity=post_office --tf reject-ways --tf reject-relations --wb file=data/to_load/po_dom_nod.osm.pbf

curl -o data/downloaded/france.osm.pbf -x 192.168.221.2:8080 http://download.geofabrik.de/openstreetmap/europe/france.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/downloaded/france.osm.pbf --tee 3 --tf accept-relations amenity=post_office --used-way idTrackerType=BitSet --used-node idTrackerType=BitSet --wb file=data/to_load/po_rel.osm.pbf --tf reject-relations --tf accept-ways amenity=post_office --used-node --wb file=data/to_load/po_way.osm.pbf --tf accept-nodes amenity=post_office --tf reject-ways --tf reject-relations --wb file=data/to_load/po_nod.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/to_load/po_rel.osm.pbf outPipe.0=r --rb file=data/to_load/po_nod.osm.pbf outPipe.0=n --merge inPipe.0=r inPipe.1=n --wb file=data/to_load/po_rel_nod.osm.pbf
./osmosis-0.40.1/bin/osmosis --rb file=data/to_load/po_rel_nod.osm.pbf outPipe.0=rn --rb file=data/to_load/po_way.osm.pbf outPipe.0=w --merge inPipe.0=rn inPipe.1=w --wb file=data/to_load/po_full.osm.pbf
osm2pgsql --database osm --username osmusr data/to_load/po_full.osm.pbf data/to_load/po_dom_*.osm.pbf -S config/osm2pgsql/post_office.style -p po -l -s -v
psql -f osm_post_office.sql osm
pg_dump -t osm_post_office --inserts -f data/to_load/insert_osm_post_office.txt osm
echo 'TRUNCATE TABLE osm_post_office;' > data/to_load/insert_osm_post_office.sql
cat data/to_load/insert_osm_post_office.txt|grep 'INSERT INTO' >> data/to_load/insert_osm_post_office.sql

