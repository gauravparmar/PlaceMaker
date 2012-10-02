--
-- Name: od_historique; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE od_historique (
    od_id character varying(50),
    categorie character varying(50),
    "timestamp" double precision
);

--
-- Name: od_postes_dept; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE od_postes_dept (
    identifiant character varying(15),
    libelle_site character varying(150),
    caracteristique_site character varying(50),
    complement_adresse character varying(100),
    adresse character varying(150),
    lieu_dit character varying(150),
    code_postal character varying(5),
    localite character varying(150),
    pays character varying(150),
    latitude double precision,
    longitude double precision,
    telephone character varying(50),
    changeur_monnaie character(1),
    photocopieur character(1),
    dab character(1),
    affranchissement_libre_service character(1),
    recharge_moneo character(1),
    monnaie_paris character(1),
    code_dept character varying(3)
);

--
-- Name: od_statut_donnees; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE od_statut_donnees (
    od_id character varying(50),
    categorie character varying(50),
    "timestamp" double precision,
    ref_statut smallint,
    dept character varying(5)
);
CREATE INDEX idx_statut_id_hash ON od_statut_donnees USING hash (od_id);

--
-- Name: osm_post_office; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE osm_post_office (
    osm_id double precision NOT NULL,
    geom_type character(1) NOT NULL,
    latitude double precision NOT NULL,
    longitude double precision NOT NULL,
    identifiant character varying(15),
    code_dept character varying(3),
    distance integer
);

--
-- Name: osm_version_base; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE osm_version_base (
    "timestamp" double precision,
    categorie character varying(50),
    date_en_clair character varying(30)
);

SET default_with_oids = true;

--
-- Name: statut_donnees; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE statut_donnees (
    id character varying(50),
    categorie character varying(50),
    "timestamp" double precision
);

--
-- Name: version_base; Type: TABLE; Schema: public; Owner: osm.vdct; Tablespace: 
--

CREATE TABLE version_base (
    "timestamp" double precision,
    date_en_clair character varying(120)
);

