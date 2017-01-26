ALTER TABLE `komponenty`
ADD INDEX `datetime_change` (`datetime_change`);

ALTER TABLE `seriova_cisla`
ADD INDEX `id_komponenty` (`id_komponenty`),
ADD UNIQUE `seriove_cislo` (`seriove_cislo`);

