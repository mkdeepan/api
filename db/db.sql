create table userform
(
    id          INT unsigned NOT NULL AUTO_INCREMENT,
    firstname   varchar(100) NOT NULL,
    lastname    varchar(100) NOT NULL,
    district    varchar(100) NOT NULL,
    designation varchar(100) NOT NULL,
    phonenumber varchar(100) NOT NULL,
    address     varchar(100) NOT NULL,
    image       LONGBLOB,
    PRIMARY KEY (id)
)
