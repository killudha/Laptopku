--
-- PostgreSQL database dump
--

-- Dumped from database version 17.2
-- Dumped by pg_dump version 17.2

-- Started on 2024-12-05 15:03:50

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 232 (class 1259 OID 24960)
-- Name: carts; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.carts (
    id_cart integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    kuantitas integer NOT NULL,
    CONSTRAINT carts_kuantitas_check CHECK ((kuantitas > 0))
);


ALTER TABLE public.carts OWNER TO postgres;

--
-- TOC entry 231 (class 1259 OID 24959)
-- Name: carts_id_cart_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.carts_id_cart_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.carts_id_cart_seq OWNER TO postgres;

--
-- TOC entry 4889 (class 0 OID 0)
-- Dependencies: 231
-- Name: carts_id_cart_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.carts_id_cart_seq OWNED BY public.carts.id_cart;


--
-- TOC entry 228 (class 1259 OID 24927)
-- Name: keluar_product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.keluar_product (
    id_keluar integer NOT NULL,
    id_product integer NOT NULL,
    tanggal_keluar date NOT NULL,
    kuantitas integer NOT NULL,
    CONSTRAINT keluar_product_kuantitas_check CHECK ((kuantitas > 0))
);


ALTER TABLE public.keluar_product OWNER TO postgres;

--
-- TOC entry 227 (class 1259 OID 24926)
-- Name: keluar_product_id_keluar_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.keluar_product_id_keluar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.keluar_product_id_keluar_seq OWNER TO postgres;

--
-- TOC entry 4890 (class 0 OID 0)
-- Dependencies: 227
-- Name: keluar_product_id_keluar_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.keluar_product_id_keluar_seq OWNED BY public.keluar_product.id_keluar;


--
-- TOC entry 226 (class 1259 OID 24914)
-- Name: masuk_product; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.masuk_product (
    id_masuk integer NOT NULL,
    id_product integer NOT NULL,
    tanggal_masuk date NOT NULL,
    kuantitas integer NOT NULL,
    CONSTRAINT masuk_product_kuantitas_check CHECK ((kuantitas > 0))
);


ALTER TABLE public.masuk_product OWNER TO postgres;

--
-- TOC entry 225 (class 1259 OID 24913)
-- Name: masuk_product_id_masuk_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.masuk_product_id_masuk_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.masuk_product_id_masuk_seq OWNER TO postgres;

--
-- TOC entry 4891 (class 0 OID 0)
-- Dependencies: 225
-- Name: masuk_product_id_masuk_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.masuk_product_id_masuk_seq OWNED BY public.masuk_product.id_masuk;


--
-- TOC entry 222 (class 1259 OID 24881)
-- Name: orders; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.orders (
    id_order integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    id_keluar integer,
    recipent_name character varying(100) NOT NULL,
    phone character varying(20) NOT NULL,
    address text NOT NULL,
    product_price numeric(15,2) NOT NULL,
    total_price numeric(15,2) NOT NULL,
    shipping_type character varying(50),
    resi character varying(50),
    payment_status character varying(20) NOT NULL,
    email character varying(100) NOT NULL,
    CONSTRAINT orders_payment_status_check CHECK (((payment_status)::text = ANY ((ARRAY['dibayar'::character varying, 'belum_dibayar'::character varying])::text[])))
);


ALTER TABLE public.orders OWNER TO postgres;

--
-- TOC entry 221 (class 1259 OID 24880)
-- Name: orders_id_order_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.orders_id_order_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.orders_id_order_seq OWNER TO postgres;

--
-- TOC entry 4892 (class 0 OID 0)
-- Dependencies: 221
-- Name: orders_id_order_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.orders_id_order_seq OWNED BY public.orders.id_order;


--
-- TOC entry 220 (class 1259 OID 24870)
-- Name: products; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.products (
    id_product integer NOT NULL,
    merek character varying(100) NOT NULL,
    tipe character varying(100) NOT NULL,
    ssd_hdd character varying(10) NOT NULL,
    processor character varying(100) NOT NULL,
    ram character varying NOT NULL,
    vga character varying(100),
    screen_size numeric(5,2),
    storage character varying NOT NULL,
    harga numeric(15,2) NOT NULL,
    tujuan text,
    fitur text,
    image_path text,
    stok integer DEFAULT 0,
    CONSTRAINT products_ssd_hdd_check CHECK (((ssd_hdd)::text = ANY ((ARRAY['ssd'::character varying, 'hdd'::character varying])::text[])))
);


ALTER TABLE public.products OWNER TO postgres;

--
-- TOC entry 219 (class 1259 OID 24869)
-- Name: products_id_product_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.products_id_product_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.products_id_product_seq OWNER TO postgres;

--
-- TOC entry 4893 (class 0 OID 0)
-- Dependencies: 219
-- Name: products_id_product_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.products_id_product_seq OWNED BY public.products.id_product;


--
-- TOC entry 230 (class 1259 OID 24940)
-- Name: reviews; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.reviews (
    id_review integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    rating integer NOT NULL,
    tanggal_ulasan date NOT NULL,
    teks_ulasan text,
    CONSTRAINT reviews_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);


ALTER TABLE public.reviews OWNER TO postgres;

--
-- TOC entry 229 (class 1259 OID 24939)
-- Name: reviews_id_review_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.reviews_id_review_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reviews_id_review_seq OWNER TO postgres;

--
-- TOC entry 4894 (class 0 OID 0)
-- Dependencies: 229
-- Name: reviews_id_review_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.reviews_id_review_seq OWNED BY public.reviews.id_review;


--
-- TOC entry 224 (class 1259 OID 24901)
-- Name: status_order; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.status_order (
    id_status integer NOT NULL,
    id_order integer NOT NULL,
    status_delivery character varying(20) NOT NULL,
    tanggal_pemesanan date NOT NULL,
    tanggal_pembayaran date,
    tanggal_pengiriman date,
    tanggal_diterima date,
    CONSTRAINT status_order_status_delivery_check CHECK (((status_delivery)::text = ANY ((ARRAY['belum_dibayar'::character varying, 'dikemas'::character varying, 'dikirim'::character varying, 'selesai'::character varying])::text[])))
);


ALTER TABLE public.status_order OWNER TO postgres;

--
-- TOC entry 223 (class 1259 OID 24900)
-- Name: status_order_id_status_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.status_order_id_status_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.status_order_id_status_seq OWNER TO postgres;

--
-- TOC entry 4895 (class 0 OID 0)
-- Dependencies: 223
-- Name: status_order_id_status_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.status_order_id_status_seq OWNED BY public.status_order.id_status;


--
-- TOC entry 218 (class 1259 OID 24855)
-- Name: users; Type: TABLE; Schema: public; Owner: postgres
--

CREATE TABLE public.users (
    id_user integer NOT NULL,
    nama_lengkap character varying(100) NOT NULL,
    username character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    password text NOT NULL,
    status character varying(20),
    role character varying(10) NOT NULL,
    CONSTRAINT users_role_check CHECK (((role)::text = ANY ((ARRAY['user'::character varying, 'admin'::character varying])::text[])))
);


ALTER TABLE public.users OWNER TO postgres;
Alter table users 
add column status character varying(20);
--
-- TOC entry 217 (class 1259 OID 24854)
-- Name: users_id_user_seq; Type: SEQUENCE; Schema: public; Owner: postgres
--

CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_user_seq OWNER TO postgres;

--
-- TOC entry 4896 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_user_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: postgres
--

ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;


--
-- TOC entry 4685 (class 2604 OID 24963)
-- Name: carts id_cart; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts ALTER COLUMN id_cart SET DEFAULT nextval('public.carts_id_cart_seq'::regclass);


--
-- TOC entry 4683 (class 2604 OID 24930)
-- Name: keluar_product id_keluar; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.keluar_product ALTER COLUMN id_keluar SET DEFAULT nextval('public.keluar_product_id_keluar_seq'::regclass);


--
-- TOC entry 4682 (class 2604 OID 24917)
-- Name: masuk_product id_masuk; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.masuk_product ALTER COLUMN id_masuk SET DEFAULT nextval('public.masuk_product_id_masuk_seq'::regclass);


--
-- TOC entry 4680 (class 2604 OID 24884)
-- Name: orders id_order; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders ALTER COLUMN id_order SET DEFAULT nextval('public.orders_id_order_seq'::regclass);


--
-- TOC entry 4678 (class 2604 OID 24873)
-- Name: products id_product; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products ALTER COLUMN id_product SET DEFAULT nextval('public.products_id_product_seq'::regclass);


--
-- TOC entry 4684 (class 2604 OID 24943)
-- Name: reviews id_review; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews ALTER COLUMN id_review SET DEFAULT nextval('public.reviews_id_review_seq'::regclass);


--
-- TOC entry 4681 (class 2604 OID 24904)
-- Name: status_order id_status; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_order ALTER COLUMN id_status SET DEFAULT nextval('public.status_order_id_status_seq'::regclass);


--
-- TOC entry 4676 (class 2604 OID 24858)
-- Name: users id_user; Type: DEFAULT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users ALTER COLUMN id_user SET DEFAULT nextval('public.users_id_user_seq'::regclass);


--
-- TOC entry 4883 (class 0 OID 24960)
-- Dependencies: 232
-- Data for Name: carts; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.carts (id_cart, id_user, id_product, kuantitas) FROM stdin;
\.


--
-- TOC entry 4879 (class 0 OID 24927)
-- Dependencies: 228
-- Data for Name: keluar_product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.keluar_product (id_keluar, id_product, tanggal_keluar, kuantitas) FROM stdin;
\.


--
-- TOC entry 4877 (class 0 OID 24914)
-- Dependencies: 226
-- Data for Name: masuk_product; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.masuk_product (id_masuk, id_product, tanggal_masuk, kuantitas) FROM stdin;
\.


--
-- TOC entry 4873 (class 0 OID 24881)
-- Dependencies: 222
-- Data for Name: orders; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.orders (id_order, id_user, id_product, id_keluar, recipent_name, phone, address, product_price, total_price, shipping_type, resi, payment_status, email) FROM stdin;
\.


--
-- TOC entry 4871 (class 0 OID 24870)
-- Dependencies: 220
-- Data for Name: products; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.products (id_product, merek, tipe, ssd_hdd, processor, ram, vga, screen_size, storage, harga, tujuan, fitur, image_path, stok) FROM stdin;
\.


--
-- TOC entry 4881 (class 0 OID 24940)
-- Dependencies: 230
-- Data for Name: reviews; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.reviews (id_review, id_user, id_product, rating, tanggal_ulasan, teks_ulasan) FROM stdin;
\.


--
-- TOC entry 4875 (class 0 OID 24901)
-- Dependencies: 224
-- Data for Name: status_order; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.status_order (id_status, id_order, status_delivery, tanggal_pemesanan, tanggal_pembayaran, tanggal_pengiriman, tanggal_diterima) FROM stdin;
\.


--
-- TOC entry 4869 (class 0 OID 24855)
-- Dependencies: 218
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: postgres
--

COPY public.users (id_user, nama_lengkap, username, email, password, status, role) FROM stdin;
1	John Doe	johndoe	john.doe@example.com	password123	active	user
2	Jane Smith	janesmith	jane.smith@example.com	password456	inactive	admin
\.


--
-- TOC entry 4897 (class 0 OID 0)
-- Dependencies: 231
-- Name: carts_id_cart_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.carts_id_cart_seq', 1, false);


--
-- TOC entry 4898 (class 0 OID 0)
-- Dependencies: 227
-- Name: keluar_product_id_keluar_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.keluar_product_id_keluar_seq', 1, false);


--
-- TOC entry 4899 (class 0 OID 0)
-- Dependencies: 225
-- Name: masuk_product_id_masuk_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.masuk_product_id_masuk_seq', 1, false);


--
-- TOC entry 4900 (class 0 OID 0)
-- Dependencies: 221
-- Name: orders_id_order_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.orders_id_order_seq', 1, false);


--
-- TOC entry 4901 (class 0 OID 0)
-- Dependencies: 219
-- Name: products_id_product_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.products_id_product_seq', 1, false);


--
-- TOC entry 4902 (class 0 OID 0)
-- Dependencies: 229
-- Name: reviews_id_review_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.reviews_id_review_seq', 1, false);


--
-- TOC entry 4903 (class 0 OID 0)
-- Dependencies: 223
-- Name: status_order_id_status_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.status_order_id_status_seq', 1, false);


--
-- TOC entry 4904 (class 0 OID 0)
-- Dependencies: 217
-- Name: users_id_user_seq; Type: SEQUENCE SET; Schema: public; Owner: postgres
--

SELECT pg_catalog.setval('public.users_id_user_seq', 9, true);


--
-- TOC entry 4713 (class 2606 OID 24966)
-- Name: carts carts_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_pkey PRIMARY KEY (id_cart);


--
-- TOC entry 4709 (class 2606 OID 24933)
-- Name: keluar_product keluar_product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.keluar_product
    ADD CONSTRAINT keluar_product_pkey PRIMARY KEY (id_keluar);


--
-- TOC entry 4707 (class 2606 OID 24920)
-- Name: masuk_product masuk_product_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.masuk_product
    ADD CONSTRAINT masuk_product_pkey PRIMARY KEY (id_masuk);


--
-- TOC entry 4703 (class 2606 OID 24889)
-- Name: orders orders_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id_order);


--
-- TOC entry 4701 (class 2606 OID 24879)
-- Name: products products_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id_product);


--
-- TOC entry 4711 (class 2606 OID 24948)
-- Name: reviews reviews_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_pkey PRIMARY KEY (id_review);


--
-- TOC entry 4705 (class 2606 OID 24907)
-- Name: status_order status_order_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_order
    ADD CONSTRAINT status_order_pkey PRIMARY KEY (id_status);


--
-- TOC entry 4695 (class 2606 OID 24868)
-- Name: users users_email_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_key UNIQUE (email);


--
-- TOC entry 4697 (class 2606 OID 24864)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id_user);


--
-- TOC entry 4699 (class 2606 OID 24866)
-- Name: users users_username_key; Type: CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);


--
-- TOC entry 4721 (class 2606 OID 24972)
-- Name: carts carts_id_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product) ON DELETE CASCADE;


--
-- TOC entry 4722 (class 2606 OID 24967)
-- Name: carts carts_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.carts
    ADD CONSTRAINT carts_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user) ON DELETE CASCADE;


--
-- TOC entry 4718 (class 2606 OID 24934)
-- Name: keluar_product keluar_product_id_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.keluar_product
    ADD CONSTRAINT keluar_product_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product) ON DELETE CASCADE;


--
-- TOC entry 4717 (class 2606 OID 24921)
-- Name: masuk_product masuk_product_id_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.masuk_product
    ADD CONSTRAINT masuk_product_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product) ON DELETE CASCADE;


--
-- TOC entry 4714 (class 2606 OID 24895)
-- Name: orders orders_id_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product) ON DELETE CASCADE;


--
-- TOC entry 4715 (class 2606 OID 24890)
-- Name: orders orders_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user) ON DELETE CASCADE;


--
-- TOC entry 4719 (class 2606 OID 24954)
-- Name: reviews reviews_id_product_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product) ON DELETE CASCADE;


--
-- TOC entry 4720 (class 2606 OID 24949)
-- Name: reviews reviews_id_user_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.reviews
    ADD CONSTRAINT reviews_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user) ON DELETE CASCADE;


--
-- TOC entry 4716 (class 2606 OID 24908)
-- Name: status_order status_order_id_order_fkey; Type: FK CONSTRAINT; Schema: public; Owner: postgres
--

ALTER TABLE ONLY public.status_order
    ADD CONSTRAINT status_order_id_order_fkey FOREIGN KEY (id_order) REFERENCES public.orders(id_order) ON DELETE CASCADE;


-- Completed on 2024-12-05 15:03:51

--
-- PostgreSQL database dump complete
--

select * from users;
alter table users
drop column status;
