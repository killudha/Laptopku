PGDMP  
    7                }            Laptopku    17.2    17.2 L               0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false                       1262    49584    Laptopku    DATABASE     �   CREATE DATABASE "Laptopku" WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'English_United States.1252';
    DROP DATABASE "Laptopku";
                     postgres    false            �            1255    49751    update_timestamp()    FUNCTION     �   CREATE FUNCTION public.update_timestamp() RETURNS trigger
    LANGUAGE plpgsql
    AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;  -- Set updated_at ke waktu saat ini
    RETURN NEW;  -- Kembalikan baris yang diperbarui
END;
$$;
 )   DROP FUNCTION public.update_timestamp();
       public               postgres    false            �            1259    49699    cart    TABLE     �   CREATE TABLE public.cart (
    id_cart integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    quantity integer NOT NULL
);
    DROP TABLE public.cart;
       public         heap r       postgres    false            �            1259    49698    cart_id_cart_seq    SEQUENCE     �   CREATE SEQUENCE public.cart_id_cart_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 '   DROP SEQUENCE public.cart_id_cart_seq;
       public               postgres    false    228                       0    0    cart_id_cart_seq    SEQUENCE OWNED BY     E   ALTER SEQUENCE public.cart_id_cart_seq OWNED BY public.cart.id_cart;
          public               postgres    false    227            �            1259    49740 
   in_product    TABLE     �   CREATE TABLE public.in_product (
    id_in integer NOT NULL,
    id_product integer NOT NULL,
    in_date date NOT NULL,
    quantity integer NOT NULL
);
    DROP TABLE public.in_product;
       public         heap r       postgres    false            �            1259    49739    in_product_id_in_seq    SEQUENCE     �   CREATE SEQUENCE public.in_product_id_in_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 +   DROP SEQUENCE public.in_product_id_in_seq;
       public               postgres    false    232                       0    0    in_product_id_in_seq    SEQUENCE OWNED BY     M   ALTER SEQUENCE public.in_product_id_in_seq OWNED BY public.in_product.id_in;
          public               postgres    false    231            �            1259    49658    orders    TABLE     3  CREATE TABLE public.orders (
    id_order integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    id_out integer,
    recipent_name character varying(255) NOT NULL,
    product_price numeric(10,2) NOT NULL,
    total_price numeric(10,2) NOT NULL,
    shipping_type character varying(255) NOT NULL,
    resi character varying(255),
    payment_status character varying(10),
    CONSTRAINT orders_payment_status_check CHECK (((payment_status)::text = ANY ((ARRAY['paid'::character varying, 'not paid'::character varying])::text[])))
);
    DROP TABLE public.orders;
       public         heap r       postgres    false            �            1259    49657    orders_id_order_seq    SEQUENCE     �   CREATE SEQUENCE public.orders_id_order_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 *   DROP SEQUENCE public.orders_id_order_seq;
       public               postgres    false    224                       0    0    orders_id_order_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE public.orders_id_order_seq OWNED BY public.orders.id_order;
          public               postgres    false    223            �            1259    49646    out_product    TABLE     �   CREATE TABLE public.out_product (
    id_out integer NOT NULL,
    id_product integer NOT NULL,
    out_date date NOT NULL,
    quantity integer NOT NULL
);
    DROP TABLE public.out_product;
       public         heap r       postgres    false            �            1259    49645    out_product_id_out_seq    SEQUENCE     �   CREATE SEQUENCE public.out_product_id_out_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.out_product_id_out_seq;
       public               postgres    false    222                        0    0    out_product_id_out_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.out_product_id_out_seq OWNED BY public.out_product.id_out;
          public               postgres    false    221            �            1259    49635    products    TABLE     �  CREATE TABLE public.products (
    id_product integer NOT NULL,
    merk character varying(255) NOT NULL,
    variety character varying(255) NOT NULL,
    ssd_hdd character varying(10),
    processor character varying(255) NOT NULL,
    ram character varying(10) NOT NULL,
    vga character varying(255),
    screen_size numeric(5,2),
    storages character varying(255) NOT NULL,
    price numeric(10,2) NOT NULL,
    purpose character varying(255),
    feature text,
    image_path character varying(255),
    stock integer DEFAULT 0 NOT NULL,
    updated_at timestamp without time zone,
    CONSTRAINT products_ssd_hdd_check CHECK (((ssd_hdd)::text = ANY ((ARRAY['SSD'::character varying, 'HDD'::character varying])::text[])))
);
    DROP TABLE public.products;
       public         heap r       postgres    false            �            1259    49634    products_id_product_seq    SEQUENCE     �   CREATE SEQUENCE public.products_id_product_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 .   DROP SEQUENCE public.products_id_product_seq;
       public               postgres    false    220            !           0    0    products_id_product_seq    SEQUENCE OWNED BY     S   ALTER SEQUENCE public.products_id_product_seq OWNED BY public.products.id_product;
          public               postgres    false    219            �            1259    49717    review    TABLE     O  CREATE TABLE public.review (
    id_review integer NOT NULL,
    id_user integer NOT NULL,
    id_product integer NOT NULL,
    rating integer,
    review_date timestamp without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL,
    review_text text NOT NULL,
    CONSTRAINT review_rating_check CHECK (((rating >= 1) AND (rating <= 5)))
);
    DROP TABLE public.review;
       public         heap r       postgres    false            �            1259    49716    review_id_review_seq    SEQUENCE     �   CREATE SEQUENCE public.review_id_review_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 +   DROP SEQUENCE public.review_id_review_seq;
       public               postgres    false    230            "           0    0    review_id_review_seq    SEQUENCE OWNED BY     M   ALTER SEQUENCE public.review_id_review_seq OWNED BY public.review.id_review;
          public               postgres    false    229            �            1259    49755    roles    TABLE     e   CREATE TABLE public.roles (
    role_id integer NOT NULL,
    name character varying(50) NOT NULL
);
    DROP TABLE public.roles;
       public         heap r       postgres    false            �            1259    49683    status_orders    TABLE     �  CREATE TABLE public.status_orders (
    id_status integer NOT NULL,
    id_order integer NOT NULL,
    status_delivery character varying(10),
    order_date date NOT NULL,
    payment_date date,
    delivery_date date,
    arrived_date date,
    CONSTRAINT status_orders_status_delivery_check CHECK (((status_delivery)::text = ANY ((ARRAY['not paid'::character varying, 'packaged'::character varying, 'shipped'::character varying, 'completed'::character varying])::text[])))
);
 !   DROP TABLE public.status_orders;
       public         heap r       postgres    false            �            1259    49682    status_orders_id_status_seq    SEQUENCE     �   CREATE SEQUENCE public.status_orders_id_status_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 2   DROP SEQUENCE public.status_orders_id_status_seq;
       public               postgres    false    226            #           0    0    status_orders_id_status_seq    SEQUENCE OWNED BY     [   ALTER SEQUENCE public.status_orders_id_status_seq OWNED BY public.status_orders.id_status;
          public               postgres    false    225            �            1259    49626    users    TABLE     [  CREATE TABLE public.users (
    id_user integer NOT NULL,
    username character varying(50),
    email character varying(100),
    password character varying(255),
    telepon character varying(100),
    alamat character varying(100),
    image_path character varying(255),
    role_id_user integer,
    updated_at timestamp without time zone
);
    DROP TABLE public.users;
       public         heap r       postgres    false            �            1259    49625    users_id_user_seq    SEQUENCE     �   CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE public.users_id_user_seq;
       public               postgres    false    218            $           0    0    users_id_user_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;
          public               postgres    false    217            O           2604    49702    cart id_cart    DEFAULT     l   ALTER TABLE ONLY public.cart ALTER COLUMN id_cart SET DEFAULT nextval('public.cart_id_cart_seq'::regclass);
 ;   ALTER TABLE public.cart ALTER COLUMN id_cart DROP DEFAULT;
       public               postgres    false    228    227    228            R           2604    49743    in_product id_in    DEFAULT     t   ALTER TABLE ONLY public.in_product ALTER COLUMN id_in SET DEFAULT nextval('public.in_product_id_in_seq'::regclass);
 ?   ALTER TABLE public.in_product ALTER COLUMN id_in DROP DEFAULT;
       public               postgres    false    231    232    232            M           2604    49661    orders id_order    DEFAULT     r   ALTER TABLE ONLY public.orders ALTER COLUMN id_order SET DEFAULT nextval('public.orders_id_order_seq'::regclass);
 >   ALTER TABLE public.orders ALTER COLUMN id_order DROP DEFAULT;
       public               postgres    false    224    223    224            L           2604    49649    out_product id_out    DEFAULT     x   ALTER TABLE ONLY public.out_product ALTER COLUMN id_out SET DEFAULT nextval('public.out_product_id_out_seq'::regclass);
 A   ALTER TABLE public.out_product ALTER COLUMN id_out DROP DEFAULT;
       public               postgres    false    221    222    222            J           2604    49638    products id_product    DEFAULT     z   ALTER TABLE ONLY public.products ALTER COLUMN id_product SET DEFAULT nextval('public.products_id_product_seq'::regclass);
 B   ALTER TABLE public.products ALTER COLUMN id_product DROP DEFAULT;
       public               postgres    false    220    219    220            P           2604    49720    review id_review    DEFAULT     t   ALTER TABLE ONLY public.review ALTER COLUMN id_review SET DEFAULT nextval('public.review_id_review_seq'::regclass);
 ?   ALTER TABLE public.review ALTER COLUMN id_review DROP DEFAULT;
       public               postgres    false    230    229    230            N           2604    49686    status_orders id_status    DEFAULT     �   ALTER TABLE ONLY public.status_orders ALTER COLUMN id_status SET DEFAULT nextval('public.status_orders_id_status_seq'::regclass);
 F   ALTER TABLE public.status_orders ALTER COLUMN id_status DROP DEFAULT;
       public               postgres    false    225    226    226            I           2604    49629    users id_user    DEFAULT     n   ALTER TABLE ONLY public.users ALTER COLUMN id_user SET DEFAULT nextval('public.users_id_user_seq'::regclass);
 <   ALTER TABLE public.users ALTER COLUMN id_user DROP DEFAULT;
       public               postgres    false    217    218    218                      0    49699    cart 
   TABLE DATA           F   COPY public.cart (id_cart, id_user, id_product, quantity) FROM stdin;
    public               postgres    false    228   �`                 0    49740 
   in_product 
   TABLE DATA           J   COPY public.in_product (id_in, id_product, in_date, quantity) FROM stdin;
    public               postgres    false    232   �`                 0    49658    orders 
   TABLE DATA           �   COPY public.orders (id_order, id_user, id_product, id_out, recipent_name, product_price, total_price, shipping_type, resi, payment_status) FROM stdin;
    public               postgres    false    224   �`                 0    49646    out_product 
   TABLE DATA           M   COPY public.out_product (id_out, id_product, out_date, quantity) FROM stdin;
    public               postgres    false    222   �`       	          0    49635    products 
   TABLE DATA           �   COPY public.products (id_product, merk, variety, ssd_hdd, processor, ram, vga, screen_size, storages, price, purpose, feature, image_path, stock, updated_at) FROM stdin;
    public               postgres    false    220   a                 0    49717    review 
   TABLE DATA           b   COPY public.review (id_review, id_user, id_product, rating, review_date, review_text) FROM stdin;
    public               postgres    false    230   �a                 0    49755    roles 
   TABLE DATA           .   COPY public.roles (role_id, name) FROM stdin;
    public               postgres    false    233   b                 0    49683    status_orders 
   TABLE DATA           �   COPY public.status_orders (id_status, id_order, status_delivery, order_date, payment_date, delivery_date, arrived_date) FROM stdin;
    public               postgres    false    226   2b                 0    49626    users 
   TABLE DATA           z   COPY public.users (id_user, username, email, password, telepon, alamat, image_path, role_id_user, updated_at) FROM stdin;
    public               postgres    false    218   Ob       %           0    0    cart_id_cart_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.cart_id_cart_seq', 1, false);
          public               postgres    false    227            &           0    0    in_product_id_in_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.in_product_id_in_seq', 1, false);
          public               postgres    false    231            '           0    0    orders_id_order_seq    SEQUENCE SET     B   SELECT pg_catalog.setval('public.orders_id_order_seq', 1, false);
          public               postgres    false    223            (           0    0    out_product_id_out_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.out_product_id_out_seq', 1, false);
          public               postgres    false    221            )           0    0    products_id_product_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.products_id_product_seq', 1, true);
          public               postgres    false    219            *           0    0    review_id_review_seq    SEQUENCE SET     C   SELECT pg_catalog.setval('public.review_id_review_seq', 1, false);
          public               postgres    false    229            +           0    0    status_orders_id_status_seq    SEQUENCE SET     J   SELECT pg_catalog.setval('public.status_orders_id_status_seq', 1, false);
          public               postgres    false    225            ,           0    0    users_id_user_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.users_id_user_seq', 1, true);
          public               postgres    false    217            b           2606    49704    cart cart_pkey 
   CONSTRAINT     Q   ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_pkey PRIMARY KEY (id_cart);
 8   ALTER TABLE ONLY public.cart DROP CONSTRAINT cart_pkey;
       public                 postgres    false    228            f           2606    49745    in_product in_product_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.in_product
    ADD CONSTRAINT in_product_pkey PRIMARY KEY (id_in);
 D   ALTER TABLE ONLY public.in_product DROP CONSTRAINT in_product_pkey;
       public                 postgres    false    232            ^           2606    49666    orders orders_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id_order);
 <   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_pkey;
       public                 postgres    false    224            \           2606    49651    out_product out_product_pkey 
   CONSTRAINT     ^   ALTER TABLE ONLY public.out_product
    ADD CONSTRAINT out_product_pkey PRIMARY KEY (id_out);
 F   ALTER TABLE ONLY public.out_product DROP CONSTRAINT out_product_pkey;
       public                 postgres    false    222            Z           2606    49644    products products_pkey 
   CONSTRAINT     \   ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id_product);
 @   ALTER TABLE ONLY public.products DROP CONSTRAINT products_pkey;
       public                 postgres    false    220            d           2606    49726    review review_pkey 
   CONSTRAINT     W   ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_pkey PRIMARY KEY (id_review);
 <   ALTER TABLE ONLY public.review DROP CONSTRAINT review_pkey;
       public                 postgres    false    230            h           2606    49759    roles roles_pkey 
   CONSTRAINT     S   ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (role_id);
 :   ALTER TABLE ONLY public.roles DROP CONSTRAINT roles_pkey;
       public                 postgres    false    233            `           2606    49689     status_orders status_orders_pkey 
   CONSTRAINT     e   ALTER TABLE ONLY public.status_orders
    ADD CONSTRAINT status_orders_pkey PRIMARY KEY (id_status);
 J   ALTER TABLE ONLY public.status_orders DROP CONSTRAINT status_orders_pkey;
       public                 postgres    false    226            X           2606    49633    users users_pkey 
   CONSTRAINT     S   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id_user);
 :   ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
       public                 postgres    false    218            t           2620    49752    products set_updated_at    TRIGGER     x   CREATE TRIGGER set_updated_at BEFORE UPDATE ON public.products FOR EACH ROW EXECUTE FUNCTION public.update_timestamp();
 0   DROP TRIGGER set_updated_at ON public.products;
       public               postgres    false    220    234            s           2620    49754    users set_updated_at    TRIGGER     u   CREATE TRIGGER set_updated_at BEFORE UPDATE ON public.users FOR EACH ROW EXECUTE FUNCTION public.update_timestamp();
 -   DROP TRIGGER set_updated_at ON public.users;
       public               postgres    false    218    234            n           2606    49710    cart cart_id_product_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product);
 C   ALTER TABLE ONLY public.cart DROP CONSTRAINT cart_id_product_fkey;
       public               postgres    false    4698    228    220            o           2606    49705    cart cart_id_user_fkey    FK CONSTRAINT     z   ALTER TABLE ONLY public.cart
    ADD CONSTRAINT cart_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user);
 @   ALTER TABLE ONLY public.cart DROP CONSTRAINT cart_id_user_fkey;
       public               postgres    false    228    4696    218            r           2606    49746 %   in_product in_product_id_product_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.in_product
    ADD CONSTRAINT in_product_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product);
 O   ALTER TABLE ONLY public.in_product DROP CONSTRAINT in_product_id_product_fkey;
       public               postgres    false    4698    220    232            j           2606    49677    orders orders_id_out_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_id_out_fkey FOREIGN KEY (id_out) REFERENCES public.out_product(id_out);
 C   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_id_out_fkey;
       public               postgres    false    222    4700    224            k           2606    49672    orders orders_id_product_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product);
 G   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_id_product_fkey;
       public               postgres    false    224    4698    220            l           2606    49667    orders orders_id_user_fkey    FK CONSTRAINT     ~   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user);
 D   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_id_user_fkey;
       public               postgres    false    224    4696    218            i           2606    49652 '   out_product out_product_id_product_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.out_product
    ADD CONSTRAINT out_product_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product);
 Q   ALTER TABLE ONLY public.out_product DROP CONSTRAINT out_product_id_product_fkey;
       public               postgres    false    222    220    4698            p           2606    49732    review review_id_product_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_id_product_fkey FOREIGN KEY (id_product) REFERENCES public.products(id_product);
 G   ALTER TABLE ONLY public.review DROP CONSTRAINT review_id_product_fkey;
       public               postgres    false    220    230    4698            q           2606    49727    review review_id_user_fkey    FK CONSTRAINT     ~   ALTER TABLE ONLY public.review
    ADD CONSTRAINT review_id_user_fkey FOREIGN KEY (id_user) REFERENCES public.users(id_user);
 D   ALTER TABLE ONLY public.review DROP CONSTRAINT review_id_user_fkey;
       public               postgres    false    218    230    4696            m           2606    49690 )   status_orders status_orders_id_order_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.status_orders
    ADD CONSTRAINT status_orders_id_order_fkey FOREIGN KEY (id_order) REFERENCES public.orders(id_order);
 S   ALTER TABLE ONLY public.status_orders DROP CONSTRAINT status_orders_id_order_fkey;
       public               postgres    false    226    4702    224                  x������ � �            x������ � �            x������ � �            x������ � �      	   �   x�%�An�0E��s��1�ݪ;lh�P)] E�0J�hJO_;���o�G���!+v旯7�mK�7%��?��@k�ZHU��Գ�����`RD!�/��^�`�Yli`O�2�fu	����Ko\P�̞�#�7�)��� � ����G�uW��<�������m)�vG^�';��4�?a]+F���4���<=��a�6�7�R?c�*�Jǰ]��=��$I���V�            x������ � �            x������ � �            x������ � �         �   x�3�L�H�˫�Rz�9Y��9鹉�9z����*F�*�*FeىY��zI��F�>�zfU�F��f��ƆE��Q��~���z�U��f~ɦ����&��F�^9z
!�E��1~��@�+F��� 1�%6     