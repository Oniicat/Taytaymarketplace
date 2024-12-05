

CREATE TABLE `activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `activity_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO activity_log VALUES("76","renzdominic6@gmail.com","Logged In","2024-12-02 20:22:09");
INSERT INTO activity_log VALUES("77","francinekatedistor@gmail.com","Logged In","2024-12-02 20:22:35");
INSERT INTO activity_log VALUES("78","malopi@gmail.com","Logged In","2024-12-03 00:29:18");
INSERT INTO activity_log VALUES("79","malopi@gmail.com","Logged In","2024-12-03 10:38:44");
INSERT INTO activity_log VALUES("80","jullianmiguel17@gmail.com","Logged In","2024-12-03 10:48:06");
INSERT INTO activity_log VALUES("81","jullianmiguel17@gmail.com","Logged In","2024-12-03 11:04:16");
INSERT INTO activity_log VALUES("82","jullianmiguel17@gmail.com","Logged In","2024-12-03 11:10:33");



CREATE TABLE `archive_accounts` (
  `seller_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  `archived_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seller_id`)
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




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
) ENGINE=MyISAM AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `category` (
  `categoryid` int NOT NULL AUTO_INCREMENT,
  `category` varchar(100) NOT NULL,
  `date_added` date NOT NULL,
  PRIMARY KEY (`categoryid`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO category VALUES("6","Clothes","2024-11-24");
INSERT INTO category VALUES("7","Footwear","2024-11-24");
INSERT INTO category VALUES("8","Toys","2024-11-24");
INSERT INTO category VALUES("10","Appliances","2024-11-24");
INSERT INTO category VALUES("11","testing","2024-12-02");
INSERT INTO category VALUES("12","burnik","2024-12-02");
INSERT INTO category VALUES("13","shet","2024-12-02");
INSERT INTO category VALUES("14","tangona","2024-12-02");



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
  KEY `shop_id` (`shop_id`),
  CONSTRAINT `profiles_ibfk_1` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`shop_id`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO profiles VALUES("73","75","malopi@gmail.com","Testing","9197741814","https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox","https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox","Binangonan");
INSERT INTO profiles VALUES("74","76","jullianmiguel17@gmail.com","tanginamo","12333443","https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox","https://mail.google.com/mail/u/0/?tab=rm&ogbl#inbox","sasdas");



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
) ENGINE=InnoDB AUTO_INCREMENT=291 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO shops VALUES("74","33","francine ","distor","distor","09284153507","Binangonan","Bading123!","katey","Bading123!","asd","","");
INSERT INTO shops VALUES("75","34","Mark","Arangoso","Calitisin","09197741814","Binangonan","Pantok","Mark Shop","123456","AB45VD","","2024-12-03 00:29:02");
INSERT INTO shops VALUES("76","35","jullian","hhddh","miguel","09284153507","Bading123!","mflskjdkjsf","miggy shop","153","4234","","2024-12-03 10:47:48");



CREATE TABLE `tb_category` (
  `category_id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(1000) NOT NULL,
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO tb_category VALUES("1","Electronics");
INSERT INTO tb_category VALUES("2","Furniture");
INSERT INTO tb_category VALUES("3","Books");
INSERT INTO tb_category VALUES("4","Clothing");
INSERT INTO tb_category VALUES("5","Toys");



CREATE TABLE `tb_product_clicks` (
  `click_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `click_count` int NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`click_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO tb_product_clicks VALUES("24","52","4","2024-12-03 11:04:44");



CREATE TABLE `tb_product_links` (
  `link_id` int NOT NULL AUTO_INCREMENT,
  `product_id` int NOT NULL,
  `link_name` varchar(500) NOT NULL,
  `links` varchar(255) NOT NULL,
  PRIMARY KEY (`link_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO tb_product_links VALUES("1","41","","https://shopee.com");
INSERT INTO tb_product_links VALUES("2","41","","https://lazada.com");
INSERT INTO tb_product_links VALUES("13","27","","www.ayokona.com");
INSERT INTO tb_product_links VALUES("20","45","","https://shopee.ph/product/368198911/14503422619?gads_t_sig=VTJGc2RHVmtYMTlxTFVSVVRrdENkVlBXTnFLbGtLY21IOVhMT0xMVGhrc2s3MjhRTUVGTDMxTmIyUFh4bU9KNXNIb1lSY0lRd0FYc0JKTVVQUmZJVWVaK3ljR3ZXckpSQkFLNWlLcGg4bEhXUEl6cGIwWnBzQ05XaUlFc1dMeFQ");
INSERT INTO tb_product_links VALUES("21","45","","https://lazada.com");
INSERT INTO tb_product_links VALUES("24","47","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("25","47","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("26","48","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("27","48","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("28","49","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("29","49","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("34","51","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("35","51","Lazada","https://lazada.com");
INSERT INTO tb_product_links VALUES("36","50","Shopee","https://shopee.com");
INSERT INTO tb_product_links VALUES("37","50","Lazada","https://lazada.com");



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
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;




CREATE TABLE `textchange` (
  `content_key` varchar(100) NOT NULL,
  `content_text` varchar(10000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`content_key`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO textchange VALUES("Address","H44M+88G, Don Hilario Cruz, Taytay, 1920 Rizal");
INSERT INTO textchange VALUES("OpeningClosing","(Closed ⋅ Opens 8 AM Sat)");
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
INSERT INTO textchange VALUES("Terms","1. Acceptance of Terms
By using this website, you agree to be bound by these Terms and Conditions and our Privacy Policy. If you do not agree to these terms, you should not use our services.

2. Registration and Account
To access certain features of the marketplace, you must create an account. You agree to provide accurate, current, and complete information during the registration process and keep it updated. You are responsible for maintaining the confidentiality of your account and password.

3. Use of the Marketplace
You agree to use the marketplace for lawful purposes only. You will not engage in activities that violate any laws, infringe on intellectual property rights, or interfere with the operation of the platform.

4. Listings and Transactions
Sellers are responsible for the accuracy of their listings. By listing a product, you confirm that you own the item and have the right to sell it. Buyers agree to purchase products based on the information provided by sellers.

Payment: All transactions are processed through [Payment Processor]. By making a purchase, buyers agree to pay the stated price, including taxes, shipping, and any applicable fees.
Shipping and Delivery: Sellers are responsible for shipping and delivering products. Delivery times and shipping charges are specified by the seller.
Returns and Refunds: Each seller sets their own return and refund policies, which are available on their product pages.
5. Prohibited Activities
You agree not to:

Post, sell, or distribute illegal, counterfeit, or infringing items.
Use the marketplace to harass, threaten, or violate the rights of other users.
Engage in fraudulent or deceptive practices.
6. Intellectual Property
All content on the website, including logos, text, images, and software, is the property of [Your Marketplace Name] or its licensors and is protected by copyright and intellectual property laws.

7. Limitation of Liability
[Your Marketplace Name] is not liable for any damages arising from your use of the marketplace, including, but not limited to, product defects, transaction disputes, or unauthorized access to your account.

8. Termination
We may suspend or terminate your account if you violate these Terms and Conditions. You may also terminate your account at any time by contacting us.

9. Privacy
We value your privacy. Please review our Privacy Policy to understand how we collect, use, and protect your personal information.

10. Changes to Terms
We may update these Terms and Conditions from time to time. Any changes will be posted on this page, and the updated date will be indicated at the top of this document. By continuing to use the marketplace after changes are posted, you agree to the revised terms.

11. Governing Law
These Terms and Conditions are governed by the laws of [Insert Country or State], without regard to its conflict of laws principles.

12. Contact Information
For questions or concerns regarding these Terms and Conditions, please contact us at: [Email Address] [Phone Number] [Physical Address]");
INSERT INTO textchange VALUES("DataPrivacy","1. Information We Collect
We collect personal information from you when you interact with our website, create an account, or make transactions. This information may include:

Personal Identification Information: Name, email address, phone number, shipping address, billing information, and payment details.
Usage Data: IP address, browser type, device information, operating system, and browsing activity on our site.
Transaction Data: Product details, purchase history, and payment history.
Communication Data: Emails, customer service inquiries, and other correspondence with us.
2. How We Use Your Information
We use the information we collect for various purposes, including:

To Provide and Improve Services: To process transactions, manage your account, and offer customer support.
To Personalize Your Experience: To suggest products and services based on your preferences.
To Communicate with You: To send order updates, promotional offers, newsletters, and other important notices. You may opt-out of marketing emails at any time.
For Legal Compliance: To comply with applicable laws and regulations, and to prevent fraud, illegal activities, and misuse of our platform.
3. How We Share Your Information
We may share your personal data with:

Third-Party Service Providers: We may share your data with payment processors, shipping partners, and other service providers necessary to process your transactions and deliver products.
Legal Compliance: We may disclose your information if required by law, such as responding to legal requests or protecting our legal rights.
Other Users: In some cases, certain information such as your name and product listings may be visible to other users of the marketplace (for example, as a seller or buyer).
4. Cookies and Tracking Technologies
We use cookies and similar tracking technologies to enhance your experience on our site, such as:

Essential Cookies: For login, session management, and maintaining preferences.
Analytics Cookies: To collect data on website usage and improve our services.
Advertising Cookies: To display relevant ads and offers based on your browsing activity.
You can manage your cookie preferences through your browser settings, but disabling cookies may affect your user experience.

5. Data Security
We use industry-standard security measures to protect your personal information from unauthorized access, disclosure, alteration, or destruction. However, please note that no method of data transmission over the Internet or electronic storage is completely secure.

6. Your Rights and Choices
Depending on your location, you may have certain rights regarding your personal data, such as:

Access and Correction: You have the right to request access to and correction of your personal information.
Data Deletion: You can request the deletion of your account and personal data, subject to certain conditions.
Opt-Out: You can opt out of receiving marketing communications by clicking the unsubscribe link in emails or adjusting your preferences in your account settings.
Data Portability: You may request to receive a copy of your personal data in a structured, commonly used format.
7. Third-Party Websites
Our website may contain links to third-party websites. This Privacy Policy applies only to our platform, and we are not responsible for the privacy practices of other sites. We recommend reviewing the privacy policies of any third-party sites you visit.

8. Children's Privacy
Our services are not intended for individuals under the age of 13. We do not knowingly collect personal data from children. If we learn that we have collected personal information from a child under 13, we will take steps to delete such information.

9. Changes to this Privacy Policy
We may update this Privacy Policy from time to time. Any changes will be posted on this page with an updated “Effective Date” at the top. We encourage you to review this Privacy Policy periodically to stay informed about how we protect your data.

10. Contact Us
If you have any questions about this Privacy Policy or how we handle your personal information, please contact us at:

Email: [Your Contact Email]
Phone: [Your Contact Phone Number]
Address: [Your Company Address]");
INSERT INTO textchange VALUES("CUBAO","By Jeepney or UV Express:
From Cubao, head to Aurora Boulevard near Cubao MRT or LRT stations.
Look for a jeepney or UV Express bound for Taytay or Angono/Binangonan and ask the driver if it passes Taytay Tiangge.
The fare is approximately ₱25-₱40, depending on the vehicle and route.
By Bus:
Take a bus heading to Antipolo/Tanay via Ortigas Extension.
Inform the conductor to drop you off at Taytay Tiangge. The bus fare is around ₱30-₱50.
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
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `otp` varchar(6) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `otp_expiry` timestamp NULL DEFAULT NULL,
  `password_reset_token` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password_reset_token_expiry` timestamp NULL DEFAULT NULL,
  `lastlogin_time` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`seller_id`),
  UNIQUE KEY `username` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO users VALUES("17","francinekatedistor","francinekatedistor@gmail.com","$2y$10$DPD2UIPlsyQdoRyH1Oxq4uMjwJx2lOe6AxPbIKjqWv33EN.633Dem","2024-11-27 10:07:31","","","","","2024-12-02 20:22:35");
INSERT INTO users VALUES("21","renzarella03","renzarella03@gmail.com","$2y$10$xtFeeSqhiryB0xV4sTqSa.tcOW3JoHZX9RkTF5IQNtJ5wEVPhGhC6","2024-12-02 17:48:15","","","","","2024-12-02 18:23:21");
INSERT INTO users VALUES("25","calitisinmarkgil20","calitisinmarkgil20@gmail.com","$2y$10$uJN3xiyXRA4D6B1EG1SyOOED02.w2i5bvFL97RFCDE0FMQmU8RDxO","2024-12-02 19:33:47","","","","","2024-12-02 19:33:47");
INSERT INTO users VALUES("26","lacandiliangelod","lacandiliangelod@gmail.com","$2y$10$rupTHTM9XyBnIr4ZOBUwIeZaZWIrTXsr25z41txFmyoGPUXzsFlC2","2024-12-02 19:36:39","","","","","2024-12-02 19:36:39");
INSERT INTO users VALUES("29","renzzdominic6","renzzdominic6@gmail.com","$2y$10$3qek5v2WZvsQPJNMhJOUOOA6UfOrVhsRfTrWNe/6.m680f6o.qEy6","2024-12-02 20:01:48","","","","","2024-12-02 20:02:53");
INSERT INTO users VALUES("30","renzdominic6","renzdominic6@gmail.com","$2y$10$1WDGPp16V/snTDpV8qAlXezwF0QK3DT6jKi/UVOudlvIatzeU5.Hq","2024-12-02 20:04:09","","","","","2024-12-02 20:22:09");
INSERT INTO users VALUES("33","","mgxcalitisin439@gmail.com","$2y$10$I5/De3Hoh4E8XobjhCdNOOOjdtfqWyOFsWyw9f52lITO.BAeTg4Iy","2024-12-03 00:09:15","","","","","2024-12-03 10:37:32");
INSERT INTO users VALUES("34","Onicat","malopi@gmail.com","$2y$10$MeNW8i/cnO9TY4Re/mDKfeCTag7WavXcZ7d5gr8kz0JRV77hayTUe","2024-12-03 00:29:02","","","","","2024-12-03 10:38:44");
INSERT INTO users VALUES("35","miggy","jullianmiguel17@gmail.com","$2y$10$cRPNwfZEvpezMn3SCf2gvOR7y7T4kQIE5Vf6piHLQ.2AhcJEl6gvS","2024-12-03 10:47:48","646171","2024-12-03 03:19:57","","","2024-12-03 11:10:33");

