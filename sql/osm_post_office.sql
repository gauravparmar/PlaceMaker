DROP TABLE IF EXISTS osm_post_office CASCADE;
CREATE TABLE osm_post_office AS
SELECT o.*,
    (ST_Distance_Spheroid(ST_SetSRID(ST_Point(o.longitude,o.latitude),4326),ST_SetSRID(ST_Point(p.longitude,p.latitude),4326),'SPHEROID["WGS 84",6378137,298.257223563]'))::integer distance
FROM    (
        SELECT  osm_id,
        geom_type,
        st_y(way) latitude,
        st_x(way) longitude,
        identifiant,
        code_dept
        FROM    (
            SELECT  p.osm_id,
                'n' geom_type,
                "ref:FR:LaPoste" identifiant,
                d.code_dept,
                p.way,
                1 rang 
            FROM    po_point p
            JOIN    tous_departements d
            ON  ST_Contains(d.geom_buffer_01_dom,p.way)
            WHERE   "ref:FR:LaPoste" IS NOT NULL
            UNION
            SELECT  p.osm_id,
                'w',
                "ref:FR:LaPoste",
                d.code_dept,
                ST_Centroid(p.way),
                rank() over(partition by osm_id order by st_x(ST_Centroid(p.way)))
            FROM    po_polygon p
            JOIN    tous_departements d
            ON  ST_Contains(d.geom_buffer_01_dom,p.way)
            WHERE   "ref:FR:LaPoste" IS NOT NULL
            UNION
            SELECT  p.osm_id,
                'n' geom_type,
                "ref:FR:LaPoste" identifiant,
                d.code_dept,
                p.way,
                1 rang
            FROM    po_point p
            JOIN    tous_departements d
            ON  ST_Contains(d.geom_buffer_01_dom,p.way)
            WHERE   "ref:FR:LaPoste" IS NULL AND
                amenity = 'post_office'
            UNION
            SELECT  p.osm_id,
                'w',
                "ref:FR:LaPoste",
                d.code_dept,
                ST_Centroid(p.way),
                rank() over(partition by osm_id order by st_x(ST_Centroid(p.way)))
            FROM    po_polygon p
            JOIN    tous_departements d
            ON  ST_Contains(d.geom_buffer_01_dom,st_buffer(p.way,0))
            WHERE   "ref:FR:LaPoste" IS NULL AND
                amenity = 'post_office'
        )a
    WHERE rang = 1
) o
LEFT OUTER JOIN od_postes_1204 p
ON  o.identifiant = p.identifiant;

