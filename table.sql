CREATE TABLE User (
    id integer primary key auto_increment,
    alias varchar(40) unique not null,
    password varchar(40) not null,
    active int(1) not null,
    administator int(1) not null
);

CREATE TABLE Booking (
    id integer primary key auto_increment,
    date date not null,
    hour time not null,
    room integer not null,
    user integer not null,
    CONSTRAINT fk_PerId FOREIGN KEY (user)
    REFERENCES User(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

ALTER TABLE Booking ADD UNIQUE INDEX datetime_user (date, hour, user);


