--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: postgres; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

--
-- Name: Activate_Tour_Guide(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Activate_Tour_Guide"(gkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$--Activate a Tour Guide Account given its key
BEGIN
 UPDATE "Tour Guide"
 SET "g_isActive" = True
 WHERE g_key = gKey;
END$$;


ALTER FUNCTION public."Activate_Tour_Guide"(gkey bigint) OWNER TO postgres;

--
-- Name: AddToCart(bigint, bigint, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "AddToCart"(tkey bigint, tskey bigint, qty integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$--Add Session to Cart and its quantity
DECLARE
tsid bigint;
BEGIN
 SELECT INTO tsid "ts_key" From "Participants" Where "t_key" = $1 AND "ts_key" = $2;
 IF found THEN
  UPDATE "Participants" SET "p_quantity" = "p_quantity" + qty,"p_isActive" = True WHERE "t_key" = $1 AND "ts_key" = $2;
 ELSE
  Insert into "Participants" ("t_key","ts_key","p_quantity")  
  Values($1,$2,$3);
 END IF;
END$_$;


ALTER FUNCTION public."AddToCart"(tkey bigint, tskey bigint, qty integer) OWNER TO postgres;

--
-- Name: AddToCart(bigint, bigint, timestamp with time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "AddToCart"(tkey bigint, tourkey bigint, tstime timestamp with time zone) RETURNS void
    LANGUAGE plpgsql
    AS $_$--Add to cart all available spaces of the given Session
DECLARE
 tsid bigint;
 qty integer;
 max integer;
BEGIN
 SELECT INTO tsid,qty "ts_key","Availability" From "Tour Session" Where "tour_key" = $2 AND "s_Time" = $3;
 IF found THEN
  Perform "AddToCart"($1,tsid,qty);
 END IF;
 
END$_$;


ALTER FUNCTION public."AddToCart"(tkey bigint, tourkey bigint, tstime timestamp with time zone) OWNER TO postgres;

--
-- Name: AddToCart1(bigint, bigint, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "AddToCart1"(tkey bigint, tskey bigint, qty integer) RETURNS integer
    LANGUAGE plpgsql
    AS $_$--Add Session to Cart and its quantity
DECLARE
tsid bigint;
pqty integer;
mx integer;
BEGIN
 SELECT INTO mx "tour_quantity" FROM "Tour Session" NATURAL JOIN "Tour" WHERE "ts_key" = $2;
 SELECT INTO tsid,pqty "ts_key","p_quantity" From "Participants" Where "t_key" = $1 AND "ts_key" = $2;
 IF found THEN
  IF mx<pqty+qty THEN
   pqty = mx - pqty;
  ELSE
   pqty = qty;
  END IF;
  UPDATE "Participants" SET "p_quantity" = "p_quantity"+pqty,"p_isActive" = True WHERE "t_key" = $1 AND "ts_key" = $2;
  RETURN pqty;
 ELSE
  Insert into "Participants" ("t_key","ts_key","p_quantity")  
  Values($1,$2,$3);
  RETURN qty;
 END IF;
END$_$;


ALTER FUNCTION public."AddToCart1"(tkey bigint, tskey bigint, qty integer) OWNER TO postgres;

--
-- Name: Admin(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Admin"(tid bigint) RETURNS void
    LANGUAGE sql
    AS $_$--Make the tourist indicated by the key, an Admin 
UPDATE "Tourist" SET "isAdmin" = true Where "t_key" = $1;$_$;


ALTER FUNCTION public."Admin"(tid bigint) OWNER TO postgres;

--
-- Name: Admin(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Admin"(email text) RETURNS void
    LANGUAGE sql
    AS $_$----Make the tourist indicated by the email, an Admin 
Update "Tourist" Set "isAdmin" = true Where "t_Email" = $1;$_$;


ALTER FUNCTION public."Admin"(email text) OWNER TO postgres;

--
-- Name: BigGroup(bigint, bigint, date, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "BigGroup"(tid bigint, tourkey bigint, day date, qty integer) RETURNS void
    LANGUAGE plpgsql
    AS $_$--Will add the quantity given of the tour given to the Cart(multiple Sessions)
DECLARE
 num integer;
 count integer;
 avi integer;
 ite timestamp with time zone;
 curs1 CURSOR FOR SELECT "ts_key" FROM "Tour Session" WHERE "tour_key" = $2 AND "s_Time"::date = $3 AND "s_isActive" Order by "s_Time";
BEGIN
 count = $4;
 FOR rec IN curs1 LOOP
   SELECT INTO avi "Availability" FROM "Tour Session" WHERE "ts_key" = rec."ts_key";
   IF count < avi THEN
    avi = count;
   END IF;
   SELECT INTO num  "AddToCart1"($1,rec."ts_key",avi);
   count = count - num;
   EXIT WHEN count < 1;
 END LOOP;
END$_$;


ALTER FUNCTION public."BigGroup"(tid bigint, tourkey bigint, day date, qty integer) OWNER TO postgres;

--
-- Name: CheckLoc(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "CheckLoc"(lcountry text, lstpr text, lcity text) RETURNS bigint
    LANGUAGE sql
    AS $_$--Returns the key of the given location
Select "L_key"
From "Location"
Where "Country" = $1 And "State-Province" = $2 And "City" = $3;$_$;


ALTER FUNCTION public."CheckLoc"(lcountry text, lstpr text, lcity text) OWNER TO postgres;

--
-- Name: Create_Category(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Create_Category"(catname text) RETURNS void
    LANGUAGE sql
    AS $_$--Creates a new category
INSERT INTO "Tour Category" ("Category_Name") 
VALUES($1)$_$;


ALTER FUNCTION public."Create_Category"(catname text) OWNER TO postgres;

--
-- Name: DailyLookAhead(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "DailyLookAhead"() RETURNS void
    LANGUAGE plpgsql
    AS $$--Runs the TS_lookahead() for every tour
DECLARE
curs1 CURSOR FOR SELECT "tour_key" FROM "Tour" WHERE "tour_isActive";
BEGIN
 FOR rec IN curs1 LOOP
  PERFORM "TS_lookAhead"(rec."tour_key");
 END LOOP;
END$$;


ALTER FUNCTION public."DailyLookAhead"() OWNER TO postgres;

--
-- Name: GetEmail(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "GetEmail"(tid bigint) RETURNS text
    LANGUAGE plpgsql
    AS $_$--Returns the email of the tourist given the key
DECLARE
 email text;
BEGIN
 Select Into email "t_Email" From "Tourist" Where "t_key" = $1;
 Return email;
END$_$;


ALTER FUNCTION public."GetEmail"(tid bigint) OWNER TO postgres;

--
-- Name: InsertLoc(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "InsertLoc"(lcountry text, lstpr text, lcity text) RETURNS bigint
    LANGUAGE sql
    AS $_$--Creates a new location
INSERT INTO "Location" ("Country", "State-Province", "City")
  Values($1, $2, $3) Returning "L_key";
$_$;


ALTER FUNCTION public."InsertLoc"(lcountry text, lstpr text, lcity text) OWNER TO postgres;

--
-- Name: Join_Category(text, bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Join_Category"(catname text, tourkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$--Pairs a Tour with a Category
DECLARE
 cid bigint;
BEGIN
 SELECT INTO cid "cat_key" From "Tour Category" Where "Category_Name" = $1;
 IF found THEN
  INSERT INTO "isCategory" (cat_key,tour_key) values (cid,$2);
 END IF;
END$_$;


ALTER FUNCTION public."Join_Category"(catname text, tourkey bigint) OWNER TO postgres;

--
-- Name: Location(text, text, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Location"(lcountry text, lstpr text, lcity text) RETURNS integer
    LANGUAGE plpgsql
    AS $$--If given Location doesn't exist in the database, it creates it and returns the key. If it exist, returns the key.
DECLARE
Lkey bigint;
BEGIN
 Select Into Lkey "L_key" From "Location" Where "Country" = upper(lcountry) And "State-Province" = upper(lstpr) And "City" = upper(lcity);
 IF found THEN
  RETURN Lkey;
 END IF;
  RETURN "InsertLoc"(upper(LCountry), upper(LStPr), upper(LCity)); 
 
END$$;


ALTER FUNCTION public."Location"(lcountry text, lstpr text, lcity text) OWNER TO postgres;

--
-- Name: QuantityPerDate(bigint, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "QuantityPerDate"(tourkey bigint, day date) RETURNS integer
    LANGUAGE sql
    AS $_$--Verify how many spaces for participants are left in the given date
Select sum("Availability")::integer From "Tour Session" Where "tour_key" =$1 AND "s_Time"::date = $2 AND "s_Time">now() - interval'4 hour' ;$_$;


ALTER FUNCTION public."QuantityPerDate"(tourkey bigint, day date) OWNER TO postgres;

--
-- Name: ReadGRep(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "ReadGRep"(grepid bigint) RETURNS void
    LANGUAGE sql
    AS $_$--Indicates that the report was read 
UPDATE "GuideReport" SET "Read" = True WHERE "gRep_key" = $1
  
$_$;


ALTER FUNCTION public."ReadGRep"(grepid bigint) OWNER TO postgres;

--
-- Name: ReadTRep(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "ReadTRep"(trepid bigint) RETURNS void
    LANGUAGE sql
    AS $_$--Indicates that the report was read 
UPDATE "Tourist Report" SET "Read" = True WHERE "tRep_key" = $1
  
$_$;


ALTER FUNCTION public."ReadTRep"(trepid bigint) OWNER TO postgres;

--
-- Name: ReportFromGuide(bigint, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "ReportFromGuide"(gkey bigint, msg text) RETURNS void
    LANGUAGE sql
    AS $_$--Send report to Admin
Insert Into "GuideReport" (g_key,"text")
 Values ($1, $2);$_$;


ALTER FUNCTION public."ReportFromGuide"(gkey bigint, msg text) OWNER TO postgres;

--
-- Name: ReportFromTourist(bigint, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "ReportFromTourist"(tid bigint, msg text) RETURNS void
    LANGUAGE sql
    AS $_$--Send Report To Admin 
Insert Into "Tourist Report" (t_key,"text")
 Values ($1, $2);
$_$;


ALTER FUNCTION public."ReportFromTourist"(tid bigint, msg text) OWNER TO postgres;

--
-- Name: Request_Activation_Guide(bigint, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Request_Activation_Guide"(gkey bigint, gemail text) RETURNS void
    LANGUAGE plpgsql
    AS $$
BEGIN
 Insert Into "GuideReport" (g_key,"text")
 Values (gKey, 'Request of Activation from: '||gemail);
  
END$$;


ALTER FUNCTION public."Request_Activation_Guide"(gkey bigint, gemail text) OWNER TO postgres;

--
-- Name: Request_Activation_Tourist(bigint, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Request_Activation_Tourist"(tkey bigint, temail text) RETURNS void
    LANGUAGE plpgsql
    AS $$BEGIN
 Insert Into "Tourist Report" (t_key,"text")
 Values (tKey, 'Request of Activation from: '||tEmail);
  
END$$;


ALTER FUNCTION public."Request_Activation_Tourist"(tkey bigint, temail text) OWNER TO postgres;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- Name: Location; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Location" (
    "L_key" bigint NOT NULL,
    "City" character varying(15) NOT NULL,
    "State-Province" character varying(15) DEFAULT 'PUERTO RICO'::character varying NOT NULL,
    "Country" character varying(25) DEFAULT 'U.S.A.'::character varying NOT NULL
);


ALTER TABLE public."Location" OWNER TO postgres;

--
-- Name: Review; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Review" (
    t_key bigint NOT NULL,
    ts_key bigint NOT NULL,
    "Text" character(140),
    "Rate" integer,
    "Date" timestamp with time zone DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    "r_isActive" boolean DEFAULT true NOT NULL
);


ALTER TABLE public."Review" OWNER TO postgres;

--
-- Name: Tour; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tour" (
    tour_key bigint NOT NULL,
    "tour_Name" character varying(50),
    "tour_Desc" character(300),
    "Facebook" character varying(200),
    "Youtube" character varying(200),
    "Instagram" character varying(200),
    "Twitter" character varying(200),
    "Duration" integer,
    g_key bigint NOT NULL,
    "tour_isActive" boolean DEFAULT true NOT NULL,
    "tour_isSuspended" boolean DEFAULT false NOT NULL,
    "Price" money,
    "L_key" bigint,
    tour_quantity integer DEFAULT 0 NOT NULL,
    extremeness integer DEFAULT 1 NOT NULL,
    tour_photo character(150) DEFAULT 'http://kiwiteam.ece.uprm.edu/NoMiddleMan/website/images/0/'::bpchar NOT NULL,
    tour_address character(200),
    "autoGen" boolean DEFAULT true NOT NULL
);


ALTER TABLE public."Tour" OWNER TO postgres;

--
-- Name: Tour Session; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tour Session" (
    ts_key bigint NOT NULL,
    tour_key bigint,
    "s_Time" timestamp with time zone,
    "s_isActive" boolean DEFAULT true NOT NULL,
    "Availability" integer
);


ALTER TABLE public."Tour Session" OWNER TO postgres;

--
-- Name: Rating; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Rating" AS
    SELECT tour.tour_key, COALESCE(avg(tts."Rate"), (0)::numeric) AS avg, count(tts."Rate") AS count FROM ("Tour" tour LEFT JOIN ("Tour Session" ts LEFT JOIN "Review" r ON ((ts.ts_key = r.ts_key))) tts ON ((tour.tour_key = tts.tour_key))) GROUP BY tour.tour_key;


ALTER TABLE public."Rating" OWNER TO postgres;

--
-- Name: Tour Guide; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tour Guide" (
    g_key bigint NOT NULL,
    "g_Email" character varying(50) NOT NULL,
    g_password character(40) NOT NULL,
    "g_FName" character varying(20) NOT NULL,
    "g_LName" character varying(20) NOT NULL,
    "g_BDate" date NOT NULL,
    "g_License" character varying(15) DEFAULT ''::character varying(15) NOT NULL,
    "Company" character varying(30),
    "g_isActive" boolean DEFAULT false NOT NULL,
    "g_isSuspended" boolean DEFAULT false NOT NULL,
    g_telephone character(12),
    g_desc character(140),
    "MemberSince" date DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    verification character(40)
);


ALTER TABLE public."Tour Guide" OWNER TO postgres;

--
-- Name: Tour Info; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Tour Info" AS
    SELECT "Tour Guide"."g_isActive", "Tour Guide".g_password, "Tour Guide".g_telephone, "Tour Guide"."g_isSuspended", "Tour Guide".g_key, "Tour Guide".g_desc, "Tour Guide"."Company", "Tour Guide"."g_BDate", "Tour Guide"."g_Email", "Tour Guide"."g_FName", "Tour Guide"."g_LName", "Tour Guide"."g_License", "Tour".tour_photo, "Tour".extremeness, "Tour".tour_address, "Tour"."tour_isActive", "Tour".tour_quantity, "Tour"."tour_isSuspended", "Tour"."L_key", "Tour"."Price", "Tour"."Twitter", "Tour"."Youtube", "Tour"."Duration", "Tour"."Facebook", "Tour".tour_key, "Tour"."Instagram", "Tour"."tour_Desc", "Tour"."tour_Name", "Location"."State-Province", "Location"."City", "Location"."Country", "Rating".avg, "Rating".count FROM ((("Tour Guide" JOIN "Tour" ON (("Tour Guide".g_key = "Tour".g_key))) JOIN "Location" ON (("Tour"."L_key" = "Location"."L_key"))) NATURAL JOIN "Rating") WHERE ((("Tour Guide"."g_isActive" = true) AND ("Tour"."tour_isActive" = true)) AND ("Tour Guide"."g_isSuspended" = false));


ALTER TABLE public."Tour Info" OWNER TO postgres;

--
-- Name: SearchByCat(text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "SearchByCat"(catname text) RETURNS SETOF "Tour Info"
    LANGUAGE sql
    AS $_$Select ti.* From "Tour Info" ti Natural Join "TourAndCategory" Where "Category_Name" = $1;$_$;


ALTER FUNCTION public."SearchByCat"(catname text) OWNER TO postgres;

--
-- Name: StartGeneration(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "StartGeneration"(tourkey bigint) RETURNS void
    LANGUAGE sql
    AS $_$UPDATE "Tour"
SET "autoGen" = true
WHERE "tour_key" = $1$_$;


ALTER FUNCTION public."StartGeneration"(tourkey bigint) OWNER TO postgres;

--
-- Name: StopGeneration(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "StopGeneration"(tourkey bigint) RETURNS void
    LANGUAGE sql
    AS $_$UPDATE "Tour"
SET "autoGen" = false
WHERE "tour_key" = $1$_$;


ALTER FUNCTION public."StopGeneration"(tourkey bigint) OWNER TO postgres;

--
-- Name: TS_Generate(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "TS_Generate"(tourkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$DECLARE
ite integer;
day integer := 0;
ver boolean;
BEGIN
UPDATE "Tour Session" SET "s_isActive"=false WHERE "tour_key" = $1;
WHILE day<7 LOOP
  SELECT INTO ver "TS_lookAhead"($1,day);
  IF found THEN 
   ite = 7;
   
   WHILE ite<281 LOOP

    Perform "TS_lookAhead"($1,(day+ite));
    ite = ite + 7;

   END LOOP;
  END IF;
  
 
day = day +1;
END LOOP; 
 
 
END$_$;


ALTER FUNCTION public."TS_Generate"(tourkey bigint) OWNER TO postgres;

--
-- Name: TS_day(bigint, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "TS_day"(tourkey bigint, day date) RETURNS void
    LANGUAGE plpgsql
    AS $_$--Generates the tour session of a tour on the given day
DECLARE
ver boolean;
dur integer;
itT timestamp with time zone;
endt timestamp with time zone;
BEGIN
 SELECT INTO itT,endt,dur "startTime" + day,"endTime" + day,"Duration" FROM "Tour Days" WHERE tour_key = tourkey and dayname::character(10) = to_char($2,'Day')::character(10);
 --PERFORM "TS_ins"(tourkey::bigint,itT);
 PERFORM "clearDay"($1,$2,itT::time with time zone,endt::time with time zone);
 SELECT INTO ver "autoGen" FROM "Tour" WHERE "tour_key"=$1;
 IF ver THEN
  WHILE itT<endT LOOP
   PERFORM "TS_ins"(tourkey::bigint,itT);
   itT = itT + dur * interval'1 minute';
  END LOOP;
 END IF;

END$_$;


ALTER FUNCTION public."TS_day"(tourkey bigint, day date) OWNER TO postgres;

--
-- Name: TS_ins(bigint, timestamp with time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "TS_ins"(tourkey bigint, stime timestamp with time zone) RETURNS void
    LANGUAGE plpgsql
    AS $_$--If a Tour Session with the given time doesn't exist it creates it 
DECLARE
 tskey bigint;
 qty integer;
BEGIN
 SELECT INTO qty "tour_quantity" FROM "Tour" WHERE "tour_key"=$1;
 SELECT INTO tskey "ts_key" FROM "Tour Session" WHERE "tour_key" = $1 AND "s_Time" = $2;
 IF found THEN
  UPDATE "Tour Session" SET "s_isActive" = True WHERE "ts_key" = tskey;
  RETURN;
 ELSE
  INSERT INTO "Tour Session"(tour_key, "s_Time", "Availability")
  Values ($1,$2,qty);
 END IF;
 RETURN;
END$_$;


ALTER FUNCTION public."TS_ins"(tourkey bigint, stime timestamp with time zone) OWNER TO postgres;

--
-- Name: TS_lookAhead(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "TS_lookAhead"(tourkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$--It Verifies if given tour has a session 287 days from now, if true then it runs TS_day for that day
DECLARE
 ver boolean;
 ver2 boolean;
BEGIN
 SELECT INTO ver to_char(current_date + interval '287 day', 'Day')::character(10) 
 IN(
 SELECT dayname
 FROM "Tour Days"
 WHERE "tour_key" = $1) as dayver;
 SELECT INTO ver2 "autoGen" From "Tour" Where "tour_key" = $1;
 IF (ver AND ver2) THEN
  Perform "TS_day"(tourkey,(current_date + interval '287 day')::date);
 END IF;
END$_$;


ALTER FUNCTION public."TS_lookAhead"(tourkey bigint) OWNER TO postgres;

--
-- Name: TS_lookAhead(bigint, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "TS_lookAhead"(tourkey bigint, ite integer) RETURNS boolean
    LANGUAGE plpgsql
    AS $_$--It Verifies if given tour has a session the amount of days given from now, if true then it runs TS_day for that day
DECLARE
 ver boolean;
BEGIN
 SELECT INTO ver to_char(now()-interval '4 hours' + $2 * interval '1 day', 'Day')::character(10) 
 IN(
 SELECT dayname
 FROM "Tour Days"
 WHERE "tour_key" = $1) as dayver;
 IF ver THEN
  Perform "TS_day"(tourkey,(now()-interval '4 hours' + $2 * interval '1 day')::date);
  RETURN true;
 END IF;
  RETURN false;
END$_$;


ALTER FUNCTION public."TS_lookAhead"(tourkey bigint, ite integer) OWNER TO postgres;

--
-- Name: Trigger_Available(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Trigger_Available"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
 tourid bigint;
 qty integer;
BEGIN
 SELECT INTO qty tour_quantity FROM "Tour"  WHERE tour_key = NEW.tour_key;
 NEW."Availability" = qty;
 RETURN NEW;
END$$;


ALTER FUNCTION public."Trigger_Available"() OWNER TO postgres;

--
-- Name: Trigger_Participant(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Trigger_Participant"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
 max integer;
BEGIN
 SELECT INTO max "Availability" FROM "Tour Session"  WHERE ts_key = NEW.ts_key;
 IF New."p_quantity" > max THEN
  NEW."p_quantity" = max;
 END IF;
 RETURN NEW;
 
 
END$$;


ALTER FUNCTION public."Trigger_Participant"() OWNER TO postgres;

--
-- Name: Trigger_Tour(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Trigger_Tour"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$DECLARE
 
BEGIN
 IF NEW."tour_quantity" != OLD."tour_quantity" THEN
  UPDATE "Tour Session" SET "Availability" = "Availability"+(NEW."tour_quantity"-OLD."tour_quantity") WHERE tour_key= NEW.tour_key;
 END IF; 
 RETURN NEW;
END$$;


ALTER FUNCTION public."Trigger_Tour"() OWNER TO postgres;

--
-- Name: Trigger_Workday(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "Trigger_Workday"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
 IF NEW."startTime" >= NEW."endTime" THEN
  NEW."active"='false';
 END IF;
 RETURN NEW;
END$$;


ALTER FUNCTION public."Trigger_Workday"() OWNER TO postgres;

--
-- Name: clearDay(bigint, date); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "clearDay"(tourkey bigint, day date) RETURNS void
    LANGUAGE sql
    AS $_$SELECT "clearDay"($1,$2,'11:11:11+00'::time with time zone,'11:11:11+00'::time with time zone)$_$;


ALTER FUNCTION public."clearDay"(tourkey bigint, day date) OWNER TO postgres;

--
-- Name: clearDay(bigint, integer); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "clearDay"(tourkey bigint, ite integer) RETURNS void
    LANGUAGE sql
    AS $_$SELECT "clearDay"($1,(now()- interval '4 hours'+$2 *interval '1 day')::date)$_$;


ALTER FUNCTION public."clearDay"(tourkey bigint, ite integer) OWNER TO postgres;

--
-- Name: clearDay(bigint, date, time with time zone, time with time zone); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "clearDay"(tourkey bigint, day date, stime time with time zone, etime time with time zone) RETURNS void
    LANGUAGE plpgsql
    AS $_$BEGIN
 UPDATE "Tour Session" SET "s_isActive" = False WHERE "tour_key"=$1 And "s_Time"::date = $2 And
 "s_Time"::time with time zone < $3 AND "ts_key" not in (Select "ts_key" FROM "Participants" NATURAL JOIN "Tour Session" where "Payed">0);
 UPDATE "Tour Session" SET "s_isActive" = False WHERE "tour_key"=$1 And "s_Time"::date = $2 And
 "s_Time"::time with time zone >= $4 AND "ts_key" not in (Select "ts_key" FROM "Participants" NATURAL JOIN "Tour Session" where "Payed">0);
END$_$;


ALTER FUNCTION public."clearDay"(tourkey bigint, day date, stime time with time zone, etime time with time zone) OWNER TO postgres;

--
-- Name: dayTrigger(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "dayTrigger"() RETURNS trigger
    LANGUAGE plpgsql
    AS $$BEGIN
 PERFORM "TS_Generate"(NOW."tour_key");
 RETURN NEW;
END$$;


ALTER FUNCTION public."dayTrigger"() OWNER TO postgres;

--
-- Name: deactivate_Tour_Session(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "deactivate_Tour_Session"(tsid bigint) RETURNS void
    LANGUAGE sql
    AS $_$UPDATE "Tour Session"
SET "s_isActive" = false
WHERE "tour_key" = $1$_$;


ALTER FUNCTION public."deactivate_Tour_Session"(tsid bigint) OWNER TO postgres;

--
-- Name: deactivate_cart_item(bigint, bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION deactivate_cart_item(tkey bigint, tskey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$BEGIN
   UPDATE "Participants"
   SET "p_isActive" = False,"p_quantity" = 0
   WHERE "t_key" = tkey and "ts_key" = tskey;
END$$;


ALTER FUNCTION public.deactivate_cart_item(tkey bigint, tskey bigint) OWNER TO postgres;

--
-- Name: deactivate_review(bigint, bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION deactivate_review(touristid bigint, sessionid bigint) RETURNS void
    LANGUAGE sql
    AS $_$Update "Review" Set "r_isActive" = false where "t_key"=$1 and "ts_key"=$2$_$;


ALTER FUNCTION public.deactivate_review(touristid bigint, sessionid bigint) OWNER TO postgres;

--
-- Name: deactivate_tours(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION deactivate_tours(tourkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$BEGIN
    UPDATE  "Tour"
    SET "tour_isActive" = false
    WHERE   "tour_key"= $1;
    PERFORM "deactivate_Tour_Session"($1); 
END$_$;


ALTER FUNCTION public.deactivate_tours(tourkey bigint) OWNER TO postgres;

--
-- Name: deactivate_tours_sessions(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION deactivate_tours_sessions(tourkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $$BEGIN
    UPDATE  "Tour Session"
    SET "s_isActive" = false
    WHERE   "tour_key"= "tourkey";
END$$;


ALTER FUNCTION public.deactivate_tours_sessions(tourkey bigint) OWNER TO postgres;

--
-- Name: deleteWorkday(bigint, text); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "deleteWorkday"(tourkey bigint, dayname text) RETURNS void
    LANGUAGE sql
    AS $_$UPDATE "Workdays"
SET "startTime" = '11:11:11+00'::time with time zone, "endTime" = '11:11:11+00'::time with time zone
WHERE "dayname" = $2 And "tour_key" = $1;$_$;


ALTER FUNCTION public."deleteWorkday"(tourkey bigint, dayname text) OWNER TO postgres;

--
-- Name: getEarning(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "getEarning"(gkey bigint) RETURNS money
    LANGUAGE sql
    AS $_$SELECT sum("totalPayed")*.9 as Earnings FROM "Participants" NATURAL JOIN "Tour Session" NATURAL JOIN "Tour" NATURAL JOIN "Tour Guide" WHERE "g_key" = $1 and "p_isActive" and "Payed">0;$_$;


ALTER FUNCTION public."getEarning"(gkey bigint) OWNER TO postgres;

--
-- Name: recentTourist(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "recentTourist"(tourkey bigint) RETURNS SETOF bigint
    LANGUAGE sql
    AS $_$SELECT "t_key" FROM "Tour Session" Natural Join "Participants" WHERE "tour_key"=$1 order by "Date" DESC limit 5;$_$;


ALTER FUNCTION public."recentTourist"(tourkey bigint) OWNER TO postgres;

--
-- Name: suspend_TourGuide(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "suspend_TourGuide"(tkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$BEGIN
 UPDATE "Tour Guide"
 SET "g_isSuspended" = True , "g_isActive"=False
 WHERE "g_key" = $1;
 PERFORM "suspend_Tours"($1);
END$_$;


ALTER FUNCTION public."suspend_TourGuide"(tkey bigint) OWNER TO postgres;

--
-- Name: suspend_Tourist(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "suspend_Tourist"(tkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$BEGIN
 UPDATE "Tourist"
 SET "t_isSuspended" = True
 WHERE "t_key" = $1;
END$_$;


ALTER FUNCTION public."suspend_Tourist"(tkey bigint) OWNER TO postgres;

--
-- Name: suspend_Tours(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION "suspend_Tours"(gkey bigint) RETURNS void
    LANGUAGE plpgsql
    AS $_$DECLARE
 curs1 CURSOR FOR SELECT "tour_key" FROM "Tour" WHERE "g_key" = $1;
BEGIN
 FOR rec IN curs1 LOOP
  PERFORM "deactivate_tours"(rec."tour_key");
 END LOOP;
END$_$;


ALTER FUNCTION public."suspend_Tours"(gkey bigint) OWNER TO postgres;

--
-- Name: test(); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION test() RETURNS boolean
    LANGUAGE plpgsql
    AS $$DECLARE
 price money;
 curs1 CURSOR FOR SELECT "ts_key","t_key" FROM "Participants" WHERE "Payed" > 0;
BEGIN
 FOR rec IN curs1 LOOP
  SELECT INTO price "Price" FROM "Tour" NATURAL JOIN "Tour Session" WHERE "ts_key" = rec."ts_key";
  UPDATE "Participants" SET "totalPayed" = price*"Payed" Where "ts_key" = rec."ts_key" AND "t_key" = rec."t_key" ;
 END LOOP;
 RETURN true;
 
END$$;


ALTER FUNCTION public.test() OWNER TO postgres;

--
-- Name: toured(bigint); Type: FUNCTION; Schema: public; Owner: postgres
--

CREATE FUNCTION toured(tid bigint) RETURNS SETOF bigint
    LANGUAGE sql
    AS $_$SELECT "tour_key" FROM "Tour Session" Natural Join "Participants" WHERE "t_key"=$1 order by "s_Time" DESC limit 5;$_$;


ALTER FUNCTION public.toured(tid bigint) OWNER TO postgres;

--
-- Name: Active Tour Guide; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Active Tour Guide" AS
    SELECT tg.g_key, tg."g_Email", tg.g_password, tg."g_FName", tg."g_LName", tg."g_BDate", tg."g_License", tg."Company", tg."g_isActive", tg."g_isSuspended", tg.g_telephone, tg.g_desc FROM "Tour Guide" tg WHERE (tg."g_isActive" AND (tg."g_isSuspended" = false));


ALTER TABLE public."Active Tour Guide" OWNER TO postgres;

--
-- Name: Tourist; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tourist" (
    t_key bigint NOT NULL,
    "t_Email" character varying(50) NOT NULL,
    t_password character(40) NOT NULL,
    "t_FName" character varying(20) NOT NULL,
    "t_LName" character varying(20) NOT NULL,
    "isAdmin" boolean DEFAULT false NOT NULL,
    "t_isActive" boolean DEFAULT false NOT NULL,
    "t_isSuspended" boolean DEFAULT false NOT NULL,
    t_telephone character(12),
    "t_Address" character varying(140) DEFAULT ' '::character varying NOT NULL,
    "MemberSince" date DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    verification character(40)
);


ALTER TABLE public."Tourist" OWNER TO postgres;

--
-- Name: Active Tourist; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Active Tourist" AS
    SELECT t.t_key, t."t_Email", t.t_password, t."t_FName", t."t_LName", t."isAdmin", t."t_isActive", t."t_isSuspended", t.t_telephone FROM "Tourist" t WHERE (t."t_isActive" AND (t."t_isSuspended" = false));


ALTER TABLE public."Active Tourist" OWNER TO postgres;

--
-- Name: All Reviews; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "All Reviews" AS
    SELECT tour.tour_key, r.t_key, r.ts_key, r."Text", r."Rate", r."Date" FROM (("Tour" tour NATURAL JOIN "Tour Session" ts) NATURAL JOIN "Review" r) WHERE (tour."tour_isActive" AND r."r_isActive");


ALTER TABLE public."All Reviews" OWNER TO postgres;

--
-- Name: GuideReport; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "GuideReport" (
    "gRep_key" bigint NOT NULL,
    g_key bigint,
    text character(140),
    "Date" timestamp with time zone DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    "Read" boolean DEFAULT false NOT NULL,
    "forAdmin" boolean DEFAULT true NOT NULL
);


ALTER TABLE public."GuideReport" OWNER TO postgres;

--
-- Name: GuideReportList; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "GuideReportList" AS
    SELECT "Tour Guide"."g_Email" AS email, "GuideReport".text, "GuideReport"."Date", "GuideReport"."gRep_key" AS key, 'Guide' AS type FROM ("Tour Guide" NATURAL JOIN "GuideReport") WHERE ("GuideReport"."Read" = false);


ALTER TABLE public."GuideReportList" OWNER TO postgres;

--
-- Name: GuideReport_gRep_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "GuideReport_gRep_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."GuideReport_gRep_key_seq" OWNER TO postgres;

--
-- Name: GuideReport_gRep_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "GuideReport_gRep_key_seq" OWNED BY "GuideReport"."gRep_key";


--
-- Name: GuideReports; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "GuideReports" AS
    SELECT tg."g_Email", g."gRep_key", g.g_key, g.text, g."Date", g."Read", g."forAdmin" FROM ("GuideReport" g NATURAL JOIN "Tour Guide" tg);


ALTER TABLE public."GuideReports" OWNER TO postgres;

--
-- Name: Location_L_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Location_L_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Location_L_key_seq" OWNER TO postgres;

--
-- Name: Location_L_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Location_L_key_seq" OWNED BY "Location"."L_key";


--
-- Name: Participants; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Participants" (
    t_key bigint NOT NULL,
    ts_key bigint NOT NULL,
    p_quantity integer NOT NULL,
    "p_isActive" boolean DEFAULT true NOT NULL,
    "Date" date DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    "Payed" integer DEFAULT 0 NOT NULL,
    "totalPayed" money DEFAULT 0 NOT NULL
);


ALTER TABLE public."Participants" OWNER TO postgres;

--
-- Name: Past Tour; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Past Tour" AS
    SELECT "Tour Session".tour_key, "Participants".ts_key, "Participants".t_key, "Participants".p_quantity, "Participants"."p_isActive", "Participants"."Date", "Tour Session"."s_Time", "Tour Session"."s_isActive", "Tour Session"."Availability", "Tour"."tour_Name", "Tour"."tour_Desc", "Tour"."Facebook", "Tour"."Youtube", "Tour"."Instagram", "Tour"."Twitter", "Tour"."Duration", "Tour".g_key, "Tour"."tour_isActive", "Tour"."tour_isSuspended", "Tour"."Price", "Tour"."L_key", "Tour".tour_quantity, "Tour".extremeness, "Tour".tour_photo, "Participants"."Payed", "Participants"."totalPayed" AS total FROM (("Participants" NATURAL JOIN "Tour Session") NATURAL JOIN "Tour") WHERE (("Participants"."Payed" > 0) AND ("Tour Session"."s_Time" < (now() - '04:00:00'::interval)));


ALTER TABLE public."Past Tour" OWNER TO postgres;

--
-- Name: SessionDay; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "SessionDay" AS
    SELECT "Tour Session".ts_key, "Tour Session".tour_key, to_char("Tour Session"."s_Time", ('Day'::character(10))::text) AS day, "Tour"."tour_Name" FROM ("Tour Session" NATURAL JOIN "Tour");


ALTER TABLE public."SessionDay" OWNER TO postgres;

--
-- Name: Shopping Cart; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Shopping Cart" AS
    SELECT "Tour Session".tour_key, "Participants".ts_key, "Participants".t_key, "Participants".p_quantity, "Participants"."p_isActive", "Participants"."Date", "Tour Session"."s_Time", "Tour Session"."s_isActive", "Tour Session"."Availability", "Tour"."tour_Name", "Tour"."tour_Desc", "Tour"."Facebook", "Tour"."Youtube", "Tour"."Instagram", "Tour"."Twitter", "Tour"."Duration", "Tour".g_key, "Tour"."tour_isActive", "Tour"."tour_isSuspended", "Tour"."Price", "Tour"."L_key", "Tour".tour_quantity, "Tour".extremeness, "Tour".tour_photo, ("Tour"."Price" * "Participants".p_quantity) AS total, ("Tour Session"."s_Time" < (now() - '04:00:00'::interval)) AS passed, ("Tour Session"."Availability" < "Participants".p_quantity) AS isfull, "Tour Guide"."g_Email", "Participants"."totalPayed" FROM ((("Participants" NATURAL JOIN "Tour Session") NATURAL JOIN "Tour") NATURAL JOIN "Tour Guide") WHERE (("Participants".p_quantity > 0) AND "Participants"."p_isActive") ORDER BY "Participants"."Date";


ALTER TABLE public."Shopping Cart" OWNER TO postgres;

--
-- Name: Tour Category; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tour Category" (
    cat_key bigint NOT NULL,
    "Category_Name" character varying(20)
);


ALTER TABLE public."Tour Category" OWNER TO postgres;

--
-- Name: Tour Category_cat_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tour Category_cat_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tour Category_cat_key_seq" OWNER TO postgres;

--
-- Name: Tour Category_cat_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tour Category_cat_key_seq" OWNED BY "Tour Category".cat_key;


--
-- Name: Workdays; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Workdays" (
    dayname character(10) NOT NULL,
    tour_key bigint,
    "startTime" time(6) with time zone NOT NULL,
    "endTime" time(6) with time zone NOT NULL,
    active boolean DEFAULT true NOT NULL
);


ALTER TABLE public."Workdays" OWNER TO postgres;

--
-- Name: Tour Days; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Tour Days" AS
    SELECT "Workdays".dayname, "Workdays".tour_key, "Workdays"."startTime", "Workdays"."endTime", "Tour"."Duration", "Tour".tour_quantity, "Workdays".active FROM ("Workdays" NATURAL JOIN "Tour") WHERE (("Tour"."tour_isActive" AND ("Workdays"."startTime" < "Workdays"."endTime")) AND "Workdays".active);


ALTER TABLE public."Tour Days" OWNER TO postgres;

--
-- Name: Tour Guide_g_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tour Guide_g_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tour Guide_g_key_seq" OWNER TO postgres;

--
-- Name: Tour Guide_g_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tour Guide_g_key_seq" OWNED BY "Tour Guide".g_key;


--
-- Name: Tour Session_ts_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tour Session_ts_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tour Session_ts_key_seq" OWNER TO postgres;

--
-- Name: Tour Session_ts_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tour Session_ts_key_seq" OWNED BY "Tour Session".ts_key;


--
-- Name: isCategory; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "isCategory" (
    cat_key bigint NOT NULL,
    tour_key bigint NOT NULL
);


ALTER TABLE public."isCategory" OWNER TO postgres;

--
-- Name: TourAndCategory; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "TourAndCategory" AS
    SELECT t.tour_key, tc."Category_Name" FROM (("Tour" t NATURAL JOIN "isCategory") NATURAL JOIN "Tour Category" tc);


ALTER TABLE public."TourAndCategory" OWNER TO postgres;

--
-- Name: Tour_tour_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tour_tour_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tour_tour_key_seq" OWNER TO postgres;

--
-- Name: Tour_tour_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tour_tour_key_seq" OWNED BY "Tour".tour_key;


--
-- Name: Tourist Report; Type: TABLE; Schema: public; Owner: postgres; Tablespace: 
--

CREATE TABLE "Tourist Report" (
    t_key bigint,
    text character(140),
    "tRep_key" bigint NOT NULL,
    "Date" timestamp with time zone DEFAULT (now() - '04:00:00'::interval) NOT NULL,
    "Read" boolean DEFAULT false NOT NULL,
    "forAdmin" boolean DEFAULT true NOT NULL
);


ALTER TABLE public."Tourist Report" OWNER TO postgres;

--
-- Name: Tourist Report_tRep_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tourist Report_tRep_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tourist Report_tRep_key_seq" OWNER TO postgres;

--
-- Name: Tourist Report_tRep_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tourist Report_tRep_key_seq" OWNED BY "Tourist Report"."tRep_key";


--
-- Name: TouristReportList; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "TouristReportList" AS
    SELECT "Tourist"."t_Email" AS email, "Tourist Report".text, "Tourist Report"."Date", "Tourist Report"."tRep_key" AS key, 'Tourist' AS type FROM ("Tourist" NATURAL JOIN "Tourist Report") WHERE ("Tourist Report"."Read" = false);


ALTER TABLE public."TouristReportList" OWNER TO postgres;

--
-- Name: TouristReports; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "TouristReports" AS
    SELECT t."t_Email", r.t_key, r.text, r."tRep_key", r."Date", r."Read", r."forAdmin" FROM ("Tourist Report" r NATURAL JOIN "Tourist" t);


ALTER TABLE public."TouristReports" OWNER TO postgres;

--
-- Name: Tourist_t_key_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE "Tourist_t_key_seq"
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public."Tourist_t_key_seq" OWNER TO postgres;

--
-- Name: Tourist_t_key_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE "Tourist_t_key_seq" OWNED BY "Tourist".t_key;


--
-- Name: Upcoming Tours; Type: VIEW; Schema: public; Owner: postgres
--

CREATE VIEW "Upcoming Tours" AS
    SELECT "Tour Session".tour_key, "Participants".ts_key, "Participants".t_key, "Participants".p_quantity, "Participants"."p_isActive", "Participants"."Date", "Tour Session"."s_Time", "Tour Session"."s_isActive", "Tour Session"."Availability", "Tour"."tour_Name", "Tour"."tour_Desc", "Tour"."Facebook", "Tour"."Youtube", "Tour"."Instagram", "Tour"."Twitter", "Tour"."Duration", "Tour".g_key, "Tour"."tour_isActive", "Tour"."tour_isSuspended", "Tour"."Price", "Tour"."L_key", "Tour".tour_quantity, "Tour".extremeness, "Tour".tour_photo, "Participants"."Payed", "Participants"."totalPayed" AS total FROM (("Participants" NATURAL JOIN "Tour Session") NATURAL JOIN "Tour") WHERE (("Participants"."Payed" > 0) AND ("Tour Session"."s_Time" > (now() - '04:00:00'::interval)));


ALTER TABLE public."Upcoming Tours" OWNER TO postgres;

--
-- Name: gRep_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "GuideReport" ALTER COLUMN "gRep_key" SET DEFAULT nextval('"GuideReport_gRep_key_seq"'::regclass);


--
-- Name: L_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Location" ALTER COLUMN "L_key" SET DEFAULT nextval('"Location_L_key_seq"'::regclass);


--
-- Name: tour_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour" ALTER COLUMN tour_key SET DEFAULT nextval('"Tour_tour_key_seq"'::regclass);


--
-- Name: cat_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour Category" ALTER COLUMN cat_key SET DEFAULT nextval('"Tour Category_cat_key_seq"'::regclass);


--
-- Name: g_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour Guide" ALTER COLUMN g_key SET DEFAULT nextval('"Tour Guide_g_key_seq"'::regclass);


--
-- Name: ts_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour Session" ALTER COLUMN ts_key SET DEFAULT nextval('"Tour Session_ts_key_seq"'::regclass);


--
-- Name: t_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tourist" ALTER COLUMN t_key SET DEFAULT nextval('"Tourist_t_key_seq"'::regclass);


--
-- Name: tRep_key; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tourist Report" ALTER COLUMN "tRep_key" SET DEFAULT nextval('"Tourist Report_tRep_key_seq"'::regclass);


--
-- Name: CatName; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour Category"
    ADD CONSTRAINT "CatName" UNIQUE ("Category_Name");


--
-- Name: Email; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tourist"
    ADD CONSTRAINT "Email" UNIQUE ("t_Email");


--
-- Name: GuideReport_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "GuideReport"
    ADD CONSTRAINT "GuideReport_key" PRIMARY KEY ("gRep_key");


--
-- Name: Location_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Location"
    ADD CONSTRAINT "Location_pkey" PRIMARY KEY ("L_key");


--
-- Name: UniqueLoc; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Location"
    ADD CONSTRAINT "UniqueLoc" UNIQUE ("Country", "State-Province", "City");


--
-- Name: cat_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour Category"
    ADD CONSTRAINT cat_key PRIMARY KEY (cat_key);


--
-- Name: g_Email; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour Guide"
    ADD CONSTRAINT "g_Email" UNIQUE ("g_Email");


--
-- Name: g_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour Guide"
    ADD CONSTRAINT g_key PRIMARY KEY (g_key);


--
-- Name: isCatId; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "isCategory"
    ADD CONSTRAINT "isCatId" PRIMARY KEY (cat_key, tour_key);


--
-- Name: r_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Participants"
    ADD CONSTRAINT r_key PRIMARY KEY (t_key, ts_key);


--
-- Name: r_key2; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Review"
    ADD CONSTRAINT r_key2 PRIMARY KEY (t_key, ts_key);


--
-- Name: tRep_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tourist Report"
    ADD CONSTRAINT "tRep_key" PRIMARY KEY ("tRep_key");


--
-- Name: t_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tourist"
    ADD CONSTRAINT t_key PRIMARY KEY (t_key);


--
-- Name: tour_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour"
    ADD CONSTRAINT tour_key PRIMARY KEY (tour_key);


--
-- Name: ts_key; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Tour Session"
    ADD CONSTRAINT ts_key PRIMARY KEY (ts_key);


--
-- Name: uniqueDay; Type: CONSTRAINT; Schema: public; Owner: postgres; Tablespace: 
--

ALTER TABLE ONLY "Workdays"
    ADD CONSTRAINT "uniqueDay" UNIQUE (dayname, tour_key);


--
-- Name: AvailableTS; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER "AvailableTS" BEFORE INSERT ON "Tour Session" FOR EACH ROW EXECUTE PROCEDURE "Trigger_Available"();


--
-- Name: EliminateDay; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER "EliminateDay" BEFORE INSERT OR UPDATE ON "Workdays" FOR EACH ROW EXECUTE PROCEDURE "Trigger_Workday"();


--
-- Name: Participants_quantity; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER "Participants_quantity" BEFORE INSERT OR UPDATE ON "Participants" FOR EACH ROW EXECUTE PROCEDURE "Trigger_Participant"();


--
-- Name: TourParticipants; Type: TRIGGER; Schema: public; Owner: postgres
--

CREATE TRIGGER "TourParticipants" BEFORE UPDATE ON "Tour" FOR EACH ROW EXECUTE PROCEDURE "Trigger_Tour"();


--
-- Name: TourSession_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Review"
    ADD CONSTRAINT "TourSession_key" FOREIGN KEY (ts_key) REFERENCES "Tour Session"(ts_key);


--
-- Name: Tour_Session_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Participants"
    ADD CONSTRAINT "Tour_Session_key" FOREIGN KEY (ts_key) REFERENCES "Tour Session"(ts_key);


--
-- Name: Tourist_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Review"
    ADD CONSTRAINT "Tourist_key" FOREIGN KEY (t_key) REFERENCES "Tourist"(t_key);


--
-- Name: cat_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "isCategory"
    ADD CONSTRAINT cat_key FOREIGN KEY (cat_key) REFERENCES "Tour Category"(cat_key) ON DELETE CASCADE;


--
-- Name: g_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour"
    ADD CONSTRAINT g_key FOREIGN KEY (g_key) REFERENCES "Tour Guide"(g_key);


--
-- Name: guide_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "GuideReport"
    ADD CONSTRAINT guide_key FOREIGN KEY (g_key) REFERENCES "Tour Guide"(g_key) ON DELETE CASCADE;


--
-- Name: loc_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour"
    ADD CONSTRAINT loc_key FOREIGN KEY ("L_key") REFERENCES "Location"("L_key");


--
-- Name: tour_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Workdays"
    ADD CONSTRAINT tour_key FOREIGN KEY (tour_key) REFERENCES "Tour"(tour_key);


--
-- Name: tour_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tour Session"
    ADD CONSTRAINT tour_key FOREIGN KEY (tour_key) REFERENCES "Tour"(tour_key) ON DELETE CASCADE;


--
-- Name: tour_key2; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "isCategory"
    ADD CONSTRAINT tour_key2 FOREIGN KEY (tour_key) REFERENCES "Tour"(tour_key);


--
-- Name: tourist_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Participants"
    ADD CONSTRAINT tourist_key FOREIGN KEY (t_key) REFERENCES "Tourist"(t_key);


--
-- Name: tourist_key; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY "Tourist Report"
    ADD CONSTRAINT tourist_key FOREIGN KEY (t_key) REFERENCES "Tourist"(t_key) ON DELETE CASCADE;


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

