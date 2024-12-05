

CREATE TABLE `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO activity_log VALUES("86","lacandiliangelod@gmail.com","Logged In","2024-12-03 14:49:17");
INSERT INTO activity_log VALUES("87","renzdominic6@gmail.com","Logged In","2024-12-03 15:12:14");
INSERT INTO activity_log VALUES("88","renzdominic6@gmail.com","Logged In","2024-12-03 15:14:07");
INSERT INTO activity_log VALUES("89","jullianmiguel17@gmail.com","Logged In","2024-12-03 15:21:18");
INSERT INTO activity_log VALUES("90","renzdominic6@gmail.com","Logged In","2024-12-03 16:06:52");
INSERT INTO activity_log VALUES("91","admin@gmail.com","Logged In","2024-12-03 16:27:15");
INSERT INTO activity_log VALUES("92","admin@gmail.com","Logged In","2024-12-03 16:27:54");
INSERT INTO activity_log VALUES("93","admin@gmail.com","Logged In","2024-12-03 16:28:07");
INSERT INTO activity_log VALUES("94","renzdominic6@gmail.com","Logged In","2024-12-03 16:38:18");
INSERT INTO activity_log VALUES("95","renzdominic65@gmail.com","Logged In","2024-12-03 16:39:52");
INSERT INTO activity_log VALUES("96","admin@gmail.com","Logged In","2024-12-03 16:40:04");
INSERT INTO activity_log VALUES("97","admin@gmail.com","Logged In","2024-12-03 17:00:48");
INSERT INTO activity_log VALUES("98","cjadebumagat@gmail.com","Logged In","2024-12-03 17:14:44");
INSERT INTO activity_log VALUES("99","Mark123@gmail.com","Logged In","2024-12-03 17:22:21");



CREATE TABLE `archive_accounts` (
  `seller_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `archived_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seller_id`)
) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO archive_accounts VALUES("40","jullianmiguel17@gmail.com","$2y$10$Xg/38HYiuEK1jAXt9NznKulWa2zTj41uo.6.FF2mt3xuRrfGYPFk2","2024-12-03 15:21:01","2024-12-03 08:36:39");
INSERT INTO archive_accounts VALUES("106","sample@gmail.com","$2y$10$gIjoFOuQxhp96Zla8IxVzeL/bryV2lkadAUBsfou7VShmAcW1xFy2","2024-12-03 16:33:12","2024-12-03 08:33:17");



CREATE TABLE `archive_shops` (
  `shop_id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `contact_number` varchar(100) NOT NULL,
  `municipality` varchar(100) NOT NULL,
  `baranggay` varchar(100) NOT NULL,
  `shop_name` varchar(100) NOT NULL,
  `stall_number` varchar(100) NOT NULL,
  `business_permit_number` varchar(100) NOT NULL,
  `permit_image` blob NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`shop_id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO archive_shops VALUES("80","40","Jullian","Miguel","Calitisin","0913-23456778","Baras","Conception","Jewelry by Angelo","075","b2022-0524","","0000-00-00 00:00:00");
INSERT INTO archive_shops VALUES("82","106","francine ","distor","distor","09284153507","Binangonan","asd","Mark Shop","123123","Bading123!","","0000-00-00 00:00:00");



CREATE TABLE `category` (
  `categoryid` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`categoryid`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO category VALUES("6","Clothes","2024-11-24");
INSERT INTO category VALUES("7","Footwear","2024-11-24");
INSERT INTO category VALUES("8","Toys","2024-11-24");
INSERT INTO category VALUES("10","Appliances","2024-11-24");
INSERT INTO category VALUES("11","testing","2024-12-02");
INSERT INTO category VALUES("12","burnik","2024-12-02");
INSERT INTO category VALUES("13","shet","2024-12-02");
INSERT INTO category VALUES("14","tangona","2024-12-02");
INSERT INTO category VALUES("15","Footwear","2024-12-03");



CREATE TABLE `profiles` (
  `profiles_id` int NOT NULL AUTO_INCREMENT,
  `shop_id` int NOT NULL,
  `user_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `shop_description` text COLLATE utf8mb4_general_ci,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `shopee_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lazada_link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `municipality` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`profiles_id`),
  KEY `profiles_ibfk_1` (`shop_id`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=80 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO profiles VALUES("76","78","lacandiliangelod@gmail.com","Welcome to Angelo & Co, your one-stop shop for everything you need! From everyday essentials to exclusive finds, we’ve got it all under one roof. We’re committed to delivering top-quality products, exceptional value, and a seamless shopping experience. Browse our collection and discover why our customers keep coming back!","9123456789","https://shopee.ph/","https://www.lazada.com.ph/","Binangonan");
INSERT INTO profiles VALUES("77","79","renzdominic6@gmail.com","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur suscipit lacus vel augue vestibulum, nec dapibus augue aliquet. Nulla facilisi. Integer volutpat interdum massa, eget interdum urna accumsan in. Proin lacinia libero in tellus ultrices, et vehicula arcu fringilla. Suspendisse potenti. Vivamus convallis sapien at urna tristique, eget fermentum nisi tempor. Nunc elementum ex ut orci tempus, in pulvinar metus posuere."","9197741814","https://shopee.ph/","https://www.lazada.com.ph/","Taytay");
INSERT INTO profiles VALUES("79","85","cjadebumagat@gmail.com","Lorem BTS","934234324","https://shopee.ph/","https://www.lazada.com.ph/","Binangonan");



CREATE TABLE `registration` (
  `seller_info_id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int DEFAULT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `contact_number` varchar(30) NOT NULL,
  `municipality` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `baranggay` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `shop_name` varchar(255) NOT NULL,
  `stall_number` varchar(100) DEFAULT NULL,
  `business_permit_number` varchar(100) DEFAULT NULL,
  `permit_image` varchar(255) DEFAULT NULL,
  `status` enum('approved','declined','pending','') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'pending',
  PRIMARY KEY (`seller_info_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `registration_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`seller_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=299 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `shop_information` (
  `info_id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `f_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `m_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `l_name` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `contact_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `shop_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `business_permit_number` int NOT NULL,
  `stall_number` int NOT NULL,
  `shop_contact_number` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `municipality` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `barangay` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`info_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `shop_information_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`seller_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;




CREATE TABLE `shops` (
  `shop_id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `contact_number` varchar(15) DEFAULT NULL,
  `municipality` varchar(100) DEFAULT NULL,
  `baranggay` varchar(100) DEFAULT NULL,
  `shop_name` varchar(100) DEFAULT NULL,
  `stall_number` varchar(50) DEFAULT NULL,
  `business_permit_number` varchar(50) DEFAULT NULL,
  `permit_image` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  PRIMARY KEY (`shop_id`),
  KEY `seller_id` (`seller_id`)
) ENGINE=InnoDB AUTO_INCREMENT=87 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO shops VALUES("78","37","Angelo","Datangel","Lacandili","09284153507","Binangonan","Darangan","Angelo &amp; Co.","T45","HJHD642U","upload/permit_674ea27ca30553.57207911_business-permit-philippines.png","2024-12-03 14:18:02");
INSERT INTO shops VALUES("79","38","Renz","Dominic","Astrologo","09284153507","Tanay","Bayani","Renz Unique Shirts","006","8945632","upload/permit_674eae91d90026.71574347_download (1).jpg","2024-12-03 15:09:25");
INSERT INTO shops VALUES("81","103","Admin","Admin","Admin","Admin","Admin","Admin","Admin","Admin","Admin","","2024-12-03 16:26:58");
INSERT INTO shops VALUES("83","107","Renz","Arella","Astrologo","091234567","Rizal","Binangonan","Renz Shop","0086","75675","","");
INSERT INTO shops VALUES("84","108","Jade","Bu","Bumagat","09284153507","Baras","Concepcion","The Amaze Shop","H63","DSD9342","upload/permit_674ec85fd00f30.76523562_download (1).jpg","2024-12-03 17:02:55");
INSERT INTO shops VALUES("85","109","Thea","O","Golla","0917123456","Antipolo","San Jose","BTS","7","123456","upload/permit_674ecb6dad5b74.39755723_download (1).jpg","2024-12-03 17:12:47");
INSERT INTO shops VALUES("86","110","Mark","Arngoso","CAlitisin","091234567","Binangonan","Binangonan","Mar SHop","JHF123","MNJDE","","2024-12-03 17:21:56");



CREATE TABLE `tb_category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(1000) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO tb_category VALUES("1","Electronics","0000-00-00");
INSERT INTO tb_category VALUES("2","Furniture","0000-00-00");
INSERT INTO tb_category VALUES("3","Books","0000-00-00");
INSERT INTO tb_category VALUES("4","Clothing","0000-00-00");
INSERT INTO tb_category VALUES("5","Toys","0000-00-00");
INSERT INTO tb_category VALUES("31","Footwear","2024-12-03");



CREATE TABLE `tb_product_clicks` (
  `click_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `click_count` int NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`click_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tb_product_clicks VALUES("26","55","6","2024-12-03 15:22:42");
INSERT INTO tb_product_clicks VALUES("27","54","5","2024-12-03 17:17:15");
INSERT INTO tb_product_clicks VALUES("28","57","5","2024-12-03 16:50:04");
INSERT INTO tb_product_clicks VALUES("29","56","1","2024-12-03 15:16:03");
INSERT INTO tb_product_clicks VALUES("30","58","2","2024-12-03 16:50:15");
INSERT INTO tb_product_clicks VALUES("31","59","1","2024-12-03 17:16:39");



CREATE TABLE `tb_product_links` (
  `link_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `link_name` varchar(500) NOT NULL,
  `links` varchar(255) NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO tb_product_links VALUES("42","54","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("43","54","Lazada","https://www.lazada.com.ph/products/iron-bed-steel-frame-bed-bedroom-bed-metal-bed-frame-modern-simple-single-beddouble-bedqueen-bed-size-i4097255598-s26221203157.html?c=&channelLpJumpArgs=&clickTrackInfo=query%253Aqueen%252Bsize%252Bbed%252Bframe%253Bnid%");
INSERT INTO tb_product_links VALUES("44","55","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("45","55","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("46","55","Facebook Marketplace","https://www.facebook.com/");
INSERT INTO tb_product_links VALUES("47","56","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("48","56","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("49","57","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("50","57","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("51","58","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("52","58","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("53","59","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("54","59","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("55","59","tiktok","hdgadsuadasjndba");



CREATE TABLE `tb_products` (
  `product_id` int NOT NULL AUTO_INCREMENT,
  `seller_id` int NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_desc` varchar(5000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `product_price` int NOT NULL,
  `product_image` longblob NOT NULL,
  `category` varchar(1000) NOT NULL,
  `date_created` date NOT NULL,
  PRIMARY KEY (`product_id`),
  KEY `seller_id` (`seller_id`),
  CONSTRAINT `tb_products_ibfk_1` FOREIGN KEY (`seller_id`) REFERENCES `users` (`seller_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO tb_products VALUES("54","37","Bed Frame Queen Size","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur suscipit lacus vel augue vestibulum, nec dapibus augue aliquet. Nulla facilisi. Integer volutpat interdum massa, eget interdum urna accumsan in. Proin lacinia libero in tellus ultrices, et vehicula arcu fringilla. Suspendisse potenti. Vivamus convallis sapien at urna tristique, eget fermentum nisi tempor. Nunc elementum ex ut orci tempus, in pulvinar metus posuere.","7299","Content/kama.webp","Furniture","2024-12-03");
INSERT INTO tb_products VALUES("55","37","Wooden Chair","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur suscipit lacus vel augue vestibulum, nec dapibus augue aliquet. Nulla facilisi. Integer volutpat interdum massa, eget interdum urna accumsan in","599","Content/upuan.webp","Furniture","2024-12-03");
INSERT INTO tb_products VALUES("56","37","Truck Cars","Proin lacinia libero in tellus ultrices, et vehicula arcu fringilla. Suspendisse potenti. Vivamus convallis sapien at urna tristique, eget fermentum nisi tempor. Nunc elementum ex ut orci tempus, in pulvinar metus posuere."","1299","Content/download.jpg","Toys","2024-12-03");
INSERT INTO tb_products VALUES("57","38","Wooden Drawer","Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur suscipit lacus vel augue vestibulum, nec dapibus augue aliquet. Nulla facilisi. Integer volutpat interdum massa, eget interdum urna accumsan in. Proin lacinia libero in tellus ultrices, et vehicula arcu fringilla. Suspendisse potenti. Vivamus convallis sapien at urna tristique, eget fermentum nisi tempor. Nunc elementum ex ut orci tempus, in pulvinar metus posuere."","599","Content/drawer.jpg","Furniture","2024-12-03");
INSERT INTO tb_products VALUES("58","38","Red Shoes","Lorem ipsum","999","Content/Tantra-sofa-1.jpg","Footwear","2024-12-03");
INSERT INTO tb_products VALUES("59","109","AMAZE","hahahahahhaha","100","Content/download.jpg","Electronics","2024-12-03");



CREATE TABLE `textchange` (
  `content_key` varchar(100) NOT NULL,
  `content_text` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`content_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO textchange VALUES("Address","H44M+88G, Don Hilario Cruz, Taytay, 1920 Rizal");
INSERT INTO textchange VALUES("OpeningClosing","10AM-6PM");
INSERT INTO textchange VALUES("Directions","By Jeepney or UV Express:
From Cubao, head to Aurora Boulevard near Cubao MRT or LRT stations.
Look for a jeepney or UV Express bound for Taytay or Angono/Binangonan and ask the driver if it passes Taytay Tiangge.
The fare is approximately ₱25-₱40, depending on the vehicle and route.
By Bus:
Take a bus heading to Antipolo/Tanay via Ortigas Extension.
Inform the conductor to drop you off at Taytay Tiangge. The bus fare is around ₱30-₱50.
By Grab/Taxi:
Open a ride-hailing app like Grab and set Taytay Tiangge as your destination.
The fare typically ranges from ₱200-₱400, depending on traffic and time.");
INSERT INTO textchange VALUES("Terms","General Use:
1.1 By using this Website, you affirm that you are at least 18 years old or have the consent of a parent 
or legal guardian.
1.2 Users must provide accurate and up-to-date information when registering an account. You are 
responsible for maintaining the confidentiality of your account credentials.
1.3 Prohibited Activities: You agree not to:
Post or sell prohibited, counterfeit, or illegal products.
Violate intellectual property rights of others.
Use the Website for fraudulent or misleading activities.
Seller Policies:
2.1 Product Listings: Sellers must provide accurate and detailed descriptions of their products, including 
prices, sizes, colors, and availability. Misleading information is prohibited.
2.2 Compliance with Laws: Sellers are responsible for ensuring their products comply with applicable 
laws and regulations, including safety, labeling, and consumer protection standards.
2.3 Transaction Fees: The Website may charge transaction fees for sales. These will be communicated 
transparently, and by listing products, sellers agree to these fees.
2.4 Disputes with Buyers: Sellers are responsible for resolving disputes with buyers. The Website may 
mediate disputes but is not obligated to do so.
Buyer Policies:
3.1 Product Purchases: Buyers must carefully review product listings and descriptions before making 
purchases.
3.2 Payment: Buyers agree to provide accurate payment information and authorize the Website to 
process transactions.
3.3 Refunds and Returns: Buyers must adhere to the refund and return policy established by the seller. 
The Website is not responsible for enforcing or managing refunds.
Content and Intellectual Property:
4.1 User Content: By posting content (e.g., product images, reviews) on the Website, you grant us a 
non-exclusive, royalty-free, worldwide license to use, reproduce, and display such content for Website 
operations.
4.2 Website Content: All content on the Website, including text, graphics, and logos, is owned by or 
licensed to the Website and protected by copyright laws. You may not copy, modify, or distribute this 
content without permission.
Limitation of Liability:
5.1 The Website is a platform connecting buyers and sellers. We are not responsible for the quality, 
safety, legality, or authenticity of products listed on the Website.
5.2 The Website is not liable for direct, indirect, incidental, or consequential damages arising from your use of the platform.");
INSERT INTO textchange VALUES("DataPrivacy","Information We Collect:
We may collect the following types of information:
1.1 Personal Information:
Name, email address, phone number, and billing/shipping address.
Payment information (e.g., credit/debit card details), processed securely via third-party payment 
gateways.
1.2 Account Information:
Username, password, and profile details.
1.3 Usage Data:
IP address, browser type, operating system, and Website activity logs.
1.4 Cookies and Tracking Data:
Cookies, web beacons, and similar technologies for tracking user preferences and enhancing Website 
functionality.
How We Use Your Information:
We use the information collected to:
2.1 Provide and improve our services, including order processing, product showcasing, and customer 
support.
2.2 Facilitate communication between buyers and sellers.
2.3 Send transactional and promotional emails, including updates about your account or new features.
2.4 Analyze Website usage to enhance user experience and security.
2.5 Comply with legal obligations or enforce our Terms and Conditions.
How We Share Your Information:
We do not sell your personal information to third parties. However, we may share your information in 
the following situations:
3.1 With Sellers or Buyers:
Contact details shared between buyers and sellers to facilitate transactions.
3.2 Service Providers:
Trusted third-party vendors that assist in operating the Website, such as payment processors, hosting 
providers, and marketing platforms.
3.3 Legal Compliance:
When required by law, court orders, or governmental regulations.
3.4 Business Transfers:
If the Website undergoes a merger, acquisition, or asset sale, your data may be transferred as part of 
the business.
Data Security:
4.1 We implement robust technical and organizational measures to protect your data against 
unauthorized access, loss, or misuse.
4.2 Despite our efforts, no transmission or storage system can guarantee complete security. Users are 
encouraged to protect their account credentials and notify us of any suspicious activity.
Your Rights:
As a user, you have the following rights:
5.1 Deletion:
You may request the deletion of your account and personal information, subject to legal or contractual 
obligations.
5.2 Marketing Preferences:
You can opt out of promotional communications by adjusting your account settings or using the 
"Unsubscribe" link in emails.
5.3 Data Portability:
Request your data in a commonly used electronic format.
To exercise these rights, contact us at [insert contact email].
Cookies Policy:
6.1 What Are Cookies?
Cookies are small text files stored on your device to enhance browsing and Website functionality.
6.2 How We Use Cookies:
To remember preferences, improve site performance, and analyze user behavior.
6.3 Managing Cookies:
You can control or disable cookies through your browser settings, though some Website features may 
become unavailable.
Data Retention:
7.1 We retain personal information only as long as necessary to fulfill the purposes outlined in this 
policy or comply with legal requirements.
7.2 Inactive accounts may be purged after a reasonable period, following prior notification.
Third-Party Links:
Our Website may contain links to third-party sites. We are not responsible for the privacy practices of 
these external sites, and we encourage you to review their policies before sharing any personal 
information.
Changes to This Policy:
We may update this Data Privacy Policy to reflect changes in our practices or legal requirements. Users 
will be notified of significant updates, and continued use of the Website constitutes acceptance of the 
revised policy.");
INSERT INTO textchange VALUES("CUBAO","1By Jeepney or UV Express:
From Cubao, head to Aurora Boulevard near Cubao MRT or LRT stations.
Look for a jeepney or UV Express bound for Taytay or Angono/Binangonan and ask the driver if it passes Taytay Tiangge.
The fare is approximately ₱25-₱40, depending on the vehicle and route.
By Bus:
Take a bus heading to Antipolo/Tanay via Ortigas Extension.
Inform the conductor to drop you off at Taytay Tiangge.
The bus fare is around ₱30-₱50.
By Grab/Taxi:
Open a ride-hailing app like Grab and set Taytay Tiangge as your destination.
The fare typically ranges from ₱200-₱400, depending on traffic and time.");
INSERT INTO textchange VALUES("EDSA","By Jeepney or UV Express:
From EDSA Crossing (near Shaw Boulevard), head to Starmall or nearby terminals.
Look for a jeepney or UV Express bound for Taytay or Angono/Binangonan and confirm with the driver if it passes Taytay Tiangge.
The fare is usually ₱25-₱40, depending on the vehicle.
By Bus:
From the bus terminals at EDSA Crossing, take a bus heading to Antipolo/Tanay that passes through Ortigas Extension.
Tell the conductor to drop you off at Taytay Tiangge.
The fare is about ₱30-₱50.
By Grab or Taxi:
Book a ride using a ride-hailing app like Grab or hail a taxi from EDSA Crossing.
Input Taytay Tiangge as your destination.
The fare typically ranges from ₱200-₱400, depending on traffic.");



CREATE TABLE `users` (
  `seller_id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `user_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `otp_expiry` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_reset_token_expiry` timestamp NULL DEFAULT NULL,
  `lastlogin_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seller_id`),
  UNIQUE KEY `username` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=111 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("37","lacandiliangelod","lacandiliangelod@gmail.com","$2y$10$49O/ZP/8Mo57dCH2VV898OcnzIEJcSEztZ/qTvKVsqEVYBZXJhGK.","","2024-12-03 14:15:44","885529","2024-12-03 06:58:13","","","2024-12-03 14:49:17");
INSERT INTO users VALUES("38","renzdominic6","renzdominic6@gmail.com","$2y$10$.99zc9lmuYYykPerX4nhKelDMJRhSO90ZIxgaswQqFCcCr63NQUAy","","2024-12-03 15:07:51","","","","","2024-12-03 16:38:18");
INSERT INTO users VALUES("42","bazeljuiceb2","bazeljuiceb2@gmail.com","$2y$10$eSXj0YeVJx.ehfX6./BGCe6Xc.XGcfH66dQiTZQCfpTDGcpjwzouK","","2024-12-03 16:16:51","","","","","2024-12-03 16:16:51");
INSERT INTO users VALUES("103","Admin","admin@gmail.com","$2y$10$I4WxjtBvurAOD9b5YgY0vOqgTVATQ.Nr9J1EmQkYinY3tf95Yi3zS","Admin","2024-12-03 16:26:58","","","","","2024-12-03 17:00:48");
INSERT INTO users VALUES("107","","renzdominic65@gmail.com","$2y$10$dws1p5XXjxWnDePnt6NgjOmytqd/Fg4.iJrYyRkWE.SUjknsjnrSe","","2024-12-03 16:39:24","","","","","2024-12-03 16:40:22");
INSERT INTO users VALUES("108","christianjadebumagat","christianjadebumagat@gmail.com","$2y$10$nIWkl10dKxowFUdW38etn.FaIpGQzYBBrC.jztmdz5I1xNdYOHKSC","","2024-12-03 16:57:18","","","","","2024-12-03 16:57:18");
INSERT INTO users VALUES("109","cjadebumagat","cjadebumagat@gmail.com","$2y$10$uxArDEz9VDW1vlutoAPBIusCVU69aLAw5p4AS9q.AEVxhFUn4pMo6","","2024-12-03 17:10:27","","","","","2024-12-03 17:14:44");
INSERT INTO users VALUES("110","MArk","Mark123@gmail.com","$2y$10$fCCYM5rhRTg10mOQO/9kuelITdsIgH9vBUNCbp1ONf9PtzI5v3fU6","","2024-12-03 17:21:56","","","","","2024-12-03 17:22:21");

