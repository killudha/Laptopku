PGDMP      $    	        
    |            Laptopku    17.2    17.2                0    0    ENCODING    ENCODING        SET client_encoding = 'UTF8';
                           false                       0    0 
   STDSTRINGS 
   STDSTRINGS     (   SET standard_conforming_strings = 'on';
                           false                       0    0 
   SEARCHPATH 
   SEARCHPATH     8   SELECT pg_catalog.set_config('search_path', '', false);
                           false                       1262    16387    Laptopku    DATABASE     �   CREATE DATABASE "Laptopku" WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'Indonesian_Indonesia.1252';
    DROP DATABASE "Laptopku";
                     postgres    false            �            1259    16428    orders    TABLE     �  CREATE TABLE public.orders (
    id_order integer NOT NULL,
    user_id integer NOT NULL,
    recipient_name character varying(255) NOT NULL,
    phone character varying(20) NOT NULL,
    address text NOT NULL,
    product_id integer NOT NULL,
    qty integer NOT NULL,
    product_price numeric(10,2) NOT NULL,
    total_price numeric(10,2) NOT NULL,
    shipping_type character varying(50) NOT NULL,
    resi character varying(100),
    payment_status character varying(10) DEFAULT 'Pending'::character varying,
    status character varying(15) DEFAULT 'Pending'::character varying,
    created_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    updated_at timestamp without time zone DEFAULT CURRENT_TIMESTAMP,
    email character varying(255)
);
    DROP TABLE public.orders;
       public         heap r       postgres    false            �            1259    16427    orders_id_order_seq    SEQUENCE     �   CREATE SEQUENCE public.orders_id_order_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 *   DROP SEQUENCE public.orders_id_order_seq;
       public               postgres    false    222                       0    0    orders_id_order_seq    SEQUENCE OWNED BY     K   ALTER SEQUENCE public.orders_id_order_seq OWNED BY public.orders.id_order;
          public               postgres    false    221            �            1259    16389    products    TABLE     �  CREATE TABLE public.products (
    id_produk integer NOT NULL,
    merek character varying(50),
    tipe character varying(50),
    ssd_hdd character varying(50),
    processor character varying(50),
    ram character varying(50),
    vga character varying(50),
    screen_size character varying(10),
    storage character varying(50),
    harga numeric(15,2),
    tujuan character varying(50),
    fitur text,
    stock integer,
    image_path character varying(255)
);
    DROP TABLE public.products;
       public         heap r       postgres    false            �            1259    16388    products_id_produk_seq    SEQUENCE     �   CREATE SEQUENCE public.products_id_produk_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 -   DROP SEQUENCE public.products_id_produk_seq;
       public               postgres    false    218                       0    0    products_id_produk_seq    SEQUENCE OWNED BY     Q   ALTER SEQUENCE public.products_id_produk_seq OWNED BY public.products.id_produk;
          public               postgres    false    217            �            1259    16398    users    TABLE     [  CREATE TABLE public.users (
    id_user integer NOT NULL,
    nama_lengkap character varying(100) NOT NULL,
    username character varying(50) NOT NULL,
    email character varying(100) NOT NULL,
    password character varying(255) NOT NULL,
    status boolean DEFAULT true,
    role character varying(10) DEFAULT 'customer'::character varying
);
    DROP TABLE public.users;
       public         heap r       postgres    false            �            1259    16397    users_id_user_seq    SEQUENCE     �   CREATE SEQUENCE public.users_id_user_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
 (   DROP SEQUENCE public.users_id_user_seq;
       public               postgres    false    220                       0    0    users_id_user_seq    SEQUENCE OWNED BY     G   ALTER SEQUENCE public.users_id_user_seq OWNED BY public.users.id_user;
          public               postgres    false    219            e           2604    16431    orders id_order    DEFAULT     r   ALTER TABLE ONLY public.orders ALTER COLUMN id_order SET DEFAULT nextval('public.orders_id_order_seq'::regclass);
 >   ALTER TABLE public.orders ALTER COLUMN id_order DROP DEFAULT;
       public               postgres    false    221    222    222            a           2604    16392    products id_produk    DEFAULT     x   ALTER TABLE ONLY public.products ALTER COLUMN id_produk SET DEFAULT nextval('public.products_id_produk_seq'::regclass);
 A   ALTER TABLE public.products ALTER COLUMN id_produk DROP DEFAULT;
       public               postgres    false    218    217    218            b           2604    16401    users id_user    DEFAULT     n   ALTER TABLE ONLY public.users ALTER COLUMN id_user SET DEFAULT nextval('public.users_id_user_seq'::regclass);
 <   ALTER TABLE public.users ALTER COLUMN id_user DROP DEFAULT;
       public               postgres    false    219    220    220            
          0    16428    orders 
   TABLE DATA           �   COPY public.orders (id_order, user_id, recipient_name, phone, address, product_id, qty, product_price, total_price, shipping_type, resi, payment_status, status, created_at, updated_at, email) FROM stdin;
    public               postgres    false    222   :$                 0    16389    products 
   TABLE DATA           �   COPY public.products (id_produk, merek, tipe, ssd_hdd, processor, ram, vga, screen_size, storage, harga, tujuan, fitur, stock, image_path) FROM stdin;
    public               postgres    false    218   �&                 0    16398    users 
   TABLE DATA           _   COPY public.users (id_user, nama_lengkap, username, email, password, status, role) FROM stdin;
    public               postgres    false    220   �)                  0    0    orders_id_order_seq    SEQUENCE SET     A   SELECT pg_catalog.setval('public.orders_id_order_seq', 9, true);
          public               postgres    false    221                       0    0    products_id_produk_seq    SEQUENCE SET     E   SELECT pg_catalog.setval('public.products_id_produk_seq', 11, true);
          public               postgres    false    217                       0    0    users_id_user_seq    SEQUENCE SET     ?   SELECT pg_catalog.setval('public.users_id_user_seq', 9, true);
          public               postgres    false    219            q           2606    16439    orders orders_pkey 
   CONSTRAINT     V   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_pkey PRIMARY KEY (id_order);
 <   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_pkey;
       public                 postgres    false    222            k           2606    16396    products products_pkey 
   CONSTRAINT     [   ALTER TABLE ONLY public.products
    ADD CONSTRAINT products_pkey PRIMARY KEY (id_produk);
 @   ALTER TABLE ONLY public.products DROP CONSTRAINT products_pkey;
       public                 postgres    false    218            m           2606    16406    users users_pkey 
   CONSTRAINT     S   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id_user);
 :   ALTER TABLE ONLY public.users DROP CONSTRAINT users_pkey;
       public                 postgres    false    220            o           2606    16408    users users_username_key 
   CONSTRAINT     W   ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_username_key UNIQUE (username);
 B   ALTER TABLE ONLY public.users DROP CONSTRAINT users_username_key;
       public                 postgres    false    220            r           2606    16445    orders orders_product_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_product_id_fkey FOREIGN KEY (product_id) REFERENCES public.products(id_produk) ON DELETE CASCADE;
 G   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_product_id_fkey;
       public               postgres    false    4715    222    218            s           2606    16440    orders orders_user_id_fkey    FK CONSTRAINT     �   ALTER TABLE ONLY public.orders
    ADD CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES public.users(id_user) ON DELETE CASCADE;
 D   ALTER TABLE ONLY public.orders DROP CONSTRAINT orders_user_id_fkey;
       public               postgres    false    222    220    4717            
   W  x���MO�0����iO�ʇ�n@+�E�AZ!q�6Vc�ؕ����w�4����撑F�y��;���t��\da3��Y�U=���胞QLM�
^�t�E$�}fA@�48ū�}�C�<.��Wr�$E%�{Q�(��E^D��%�!�<�s�����$��+P���*�gi�Y��8��4�5���kP�A�H����F�s��X��E-߄�dN8����R�9���R���<���j��Ϡ��F�)}ֻ���9������_]�R+�J�vd-T)�������E#�z��|��4s�k�jx?j��M�=�-�>�K���ǟM2���4�HF
��$�߲n���cG/�Cٻ;�ٔ�(�j{�~���D�F��C*���t�q�s����-�-Nպ|h�����h�8ɧt.���O<��k�O������v�zC�M9j��;�
#{����aD	��v9�ʢ�?Y���KE�F�\���)"��m��O0��ѱ�e�{]:���"2���:���c���K6N�7:n���cT���[A��~wjǶ������7Fw=4���� <K���?-��;?8�,���&��_,�L         �  x���_o�0��_?E���Q���N7f2w�n;�d	�R�()h�}��R���+k<<��W[�8���H��P�II."�SZپ�-�u�;x~�]�L	u��5�+�/w@[��4-|�d	#�Jǜ�ZJ�I�͚,�&	yBD(�-�[p+c���*�
&���ˆݹ���R�<�}��d��-�c��
ҙ_y(�E|�ӕ�gG���d�R*����u����pm�R����7�x<�Ξ�#J�JP�9�$�U��z�C2����L%?�,7Zfd*�:�Gv������b�_�υ��+��x��=\d@�� Iy|RCȠ/���	��C&�XC����XK�V_d\�*-b��I(
Qh���'!����B$>�,��L.'�\��.����z�i�md�l�x��t7ie�g�Y�g2���3ƙ��6e�i	����Q�JM�����ba�,���3��^�
)Ą�D�|��T|>�g\� .��F<�\+R6�P�6a�U7Fq�����E[��|��P�wZ�� �z�)��H�¨�E|�R��c~�b�S�k�N���g��BdxXఱ�CD ��>j���`��'�D��қ��ԩ���A}<���<k�����&�5ߵ�G�@�e�]!�l%ڛܪ��TTg5ek�T�<v�ll��KevM�Gg�A0W��m+��:%-�����-�⒣�>���:$&���y���ہ���{��h��Q�           x�m��R�0Fח��t�-T�i���W]�qs	��&T��0R]�;��|7����,5��u}�_T��7
*t�16_o�P�J��N�*�w!���zj�����n�$p(�JA,���/�f�8�y���(�����-;H�_@]�h��u;�5J��M��I�W�ַP7(�I�	=�=@�Zx�Z.�>��Ћ�d���5��u��.L���X��(8_5[x���[�h�.g��T;Z&eb؛�u��{��d�"�~�����*��?�v�H     