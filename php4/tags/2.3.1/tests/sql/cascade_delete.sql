insert into test_image (id, description, title) values (12, null, '')
insert into test_image (id, description, title) values (13, null, '')
insert into test_image (id, description, title) values (14, null, '')
insert into test_image (id, description, title) values (15, 0x776F77, '')
insert into test_image (id, description, title) values (16, 0x313233, '123')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (16, 12, 'c07c5e8e842389704e9d45e728f92a19', 480, 640, 'original')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (17, 12, 'daae25a5fdaa4b22828a5849b87c8922', 75, 100, 'thumbnail')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (18, 12, 'b931d247cfb10ffa8152757c01093188', 22, 30, 'icon')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (19, 13, '4f27637110d885228094bdd3db787a0e', 480, 640, 'original')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (20, 13, '69a27c30da49ce4b9616d633fccaa5b1', 75, 100, 'thumbnail')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (21, 13, 'e2b50db13307f9f5fa03960b3426831f', 22, 30, 'icon')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (22, 14, '9cd4179937ab70431eeb3b9e3d26e681', 480, 640, 'original')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (23, 14, 'f6bbae274fb801f7612781391c64c29c', 75, 100, 'thumbnail')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (24, 14, 'd2496fb213b2fb875a03ec4698d31418', 22, 30, 'icon')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (25, 16, '15ff939511dc711751d715f54f52145c', 480, 640, 'original')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (26, 16, 'c579ef05d661dd936601d051e983fc96', 75, 100, 'thumbnail')
insert into test_image_variation (id, image_id, media_id, width, height, variation) values (27, 16, 'b0e512a1c09dd2e7c2b529dccb6385d5', 22, 30, 'icon')
insert into test_media (id, file_name, mime_type, size, etag) values ('15ff939511dc711751d715f54f52145c', '04.JPG', 'image/pjpeg', 46425, 'e007845ebdc0d617980ce0411d5fcb48')
insert into test_media (id, file_name, mime_type, size, etag) values ('4f27637110d885228094bdd3db787a0e', '02.JPG', 'image/pjpeg', 42270, '09a6f9fad2bd1a354d3927d540b0f1e5')
insert into test_media (id, file_name, mime_type, size, etag) values ('69a27c30da49ce4b9616d633fccaa5b1', '', 'image/jpeg', 2732, 'c35afec588076e0d9a4b7be8168c1f44')
insert into test_media (id, file_name, mime_type, size, etag) values ('9cd4179937ab70431eeb3b9e3d26e681', '03.JPG', 'image/pjpeg', 47919, 'ca7974ba161143cf5112fd7be23fd13f')
insert into test_media (id, file_name, mime_type, size, etag) values ('b0e512a1c09dd2e7c2b529dccb6385d5', '', 'image/jpeg', 958, '39c45c94b1359b0b6bef2531c623a315')
insert into test_media (id, file_name, mime_type, size, etag) values ('b931d247cfb10ffa8152757c01093188', '', 'image/jpeg', 993, 'cf8a097af4b27028acfb4b9e2da9999e')
insert into test_media (id, file_name, mime_type, size, etag) values ('c07c5e8e842389704e9d45e728f92a19', '05.JPG', 'image/pjpeg', 56229, '7c04b5273747b754b49d73388fc8feb9')
insert into test_media (id, file_name, mime_type, size, etag) values ('c579ef05d661dd936601d051e983fc96', '', 'image/jpeg', 2829, 'badf28f0e38d9788ada36cc9464e9807')
insert into test_media (id, file_name, mime_type, size, etag) values ('d2496fb213b2fb875a03ec4698d31418', '', 'image/jpeg', 959, '3ed18d37215ef10e8fed356e9a4206e1')
insert into test_media (id, file_name, mime_type, size, etag) values ('daae25a5fdaa4b22828a5849b87c8922', '', 'image/jpeg', 3090, 'a0a8b1085e2f224d04b4e414b7167fe8')
insert into test_media (id, file_name, mime_type, size, etag) values ('e2b50db13307f9f5fa03960b3426831f', '', 'image/jpeg', 960, 'd30efd87c4e50c6b943ed0a4d475bc75')
insert into test_media (id, file_name, mime_type, size, etag) values ('f6bbae274fb801f7612781391c64c29c', '', 'image/jpeg', 2878, '5598e14eb686d47173ea40c35087362b')