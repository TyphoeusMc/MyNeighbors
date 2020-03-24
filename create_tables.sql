-- create database if not exists neighborhood;
use neighborhood;

create table if not exists hoods
(
hid int not null,
hname char(20) not null,
city char(20) not null,
state char(12) not null,
sw_coord_la float not null,
sw_coord_lo float not null, 
ne_coord_la float not null,
ne_coord_lo float not null,
primary key (hid)
) default charset=utf8;

create table if not exists uprofile
(
profile_id int not null,
content varchar(200) not null,
primary key (profile_id)
) default charset=utf8;

create table if not exists users
(
uid int not null,
uname char(24) not null,
upassword char(32) not null,
apt char(10),
building int not null,
street char(15) not null,
ave char(10),
hood int,
uprofile int,
email char(50) not null,
notify bool,
primary key (uid),
foreign key (hood) references hoods(hid),
foreign key (uprofile) references uprofile(profile_id)
) default charset=utf8;

create table if not exists blocks
(
b_id int not null,
bname char(20) not null,
hood int,
sw_coord_la float not null,
sw_coord_lo float not null, 
ne_coord_la float not null,
ne_coord_lo float not null,
primary key (b_id),
foreign key (hood) references hoods(hid)
) default charset=utf8;

create table if not exists friendship
(
applicant int not null,
target int not null,
approved bool not null default false,
primary key (applicant, target),
foreign key (applicant) references users(uid),
foreign key (target) references users(uid)
);

create table if not exists neighborhood
(
user1 int not null,
user2 int not null,
primary key (user1, user2),
foreign key (user1) references users(uid),
foreign key (user2) references users(uid)
);

create table if not exists application
(
applicant int not null,
blocks int not null,
approved bool not null,
primary key (applicant),
foreign key (applicant) references users(uid),
foreign key (blocks) references blocks(b_id)
);

create table if not exists approval
(
users int not null,
applicant int not null,
primary key (users, applicant),
foreign key (applicant) references users(uid),
foreign key (users) references users(uid)
);

create table if not exists subjects
(
sid int,
content varchar(50),
hood int,
primary key (sid),
foreign key (hood) references hoods(hid)
);

create table if not exists thread
(
tid int not null,
topic_id int,
title char(100) not null,
ini_message int,
author int,
ptime timestamp,
building int,
street char(15),
ave char(10),
lastpost timestamp,
primary key (tid),
foreign key (topic_id) references subjects(sid),
foreign key (author) references users(uid)
) default charset=utf8;

create table if not exists message
(
m_id int,
author int not null,
mtime timestamp not null,
content varchar(512) not null,
reply_to int,
thread int not null,
primary key (m_id),
foreign key (author) references users(uid),
foreign key (thread) references thread(tid),
foreign key (reply_to) references users(uid)
) default charset=utf8;

create table if not exists recipient
(
thread int not null,
users int not null,
primary key (thread, users),
foreign key (thread) references thread(tid),
foreign key (users) references users(uid)
);

create table if not exists views
(
users int not null,
thread int not null,
vtime timestamp not null,
primary key (users, thread),
foreign key (thread) references thread(tid),
foreign key (users) references users(uid)
);



delimiter !!
create trigger update_lastpost
after insert on message
for each row
begin
	update thread
    set lastpost = new.mtime
    where tid = new.thread;
end;
!!
delimiter ;

-- new added
delimiter !!
create trigger application_initial
before insert on application
for each row
begin
	if 
    (
		select count(*) 
        from application 
        where blocks = new.blocks
	) <= 0
    then
        set new.approved = true;
    end if;
end;
!!
delimiter ;


delimiter !!
create trigger check_approve
after insert on approval
for each row
begin
	if 
    (
		select count(*) 
        from approval 
        where applicant = new.applicant
	) >= 3 or
    (
		select count(*) 
        from approval 
        where applicant = new.applicant
	) = 
    (
		select count(applicant)
        from application as a1
        where a1.blocks = ( select blocks from application as a2 where a2.applicant = new.applicant) and
              a1.approved = true
    )
    then
		update application
        set approved = true
        where applicant = new.applicant;
    end if;
end;
!!
delimiter ;

delimiter !!
create trigger id_profile
before insert on uprofile
for each row
begin
	declare max_id integer;
	select max(profile_id) into max_id from uprofile;
	if max_id is null
    then
		set new.profile_id = 1;
	else
		set new.profile_id = max_id + 1;
	end if;
end;
!!
delimiter ;


delimiter !!
create trigger id_user
before insert on users
for each row
begin
	declare max_id integer;
	select max(uid) into max_id from users;
	if max_id is null
    then
		set new.uid = 1;
	else
		set new.uid = max_id + 1;
	end if;
end;
!!
delimiter ;

delimiter !!
create trigger id_subject
before insert on subjects
for each row
begin
	declare max_id integer;
	select max(sid) into max_id from subjects;
	if max_id is null
    then
		set new.sid = 1;
	else
		set new.sid = max_id + 1;
	end if;
end;
!!
delimiter ;

delimiter !!
create trigger id_thread
before insert on thread
for each row
begin
	declare max_id integer;
	select max(tid) into max_id from thread;
	if max_id is null
    then
		set new.tid = 1;
	else
		set new.tid = max_id + 1;
	end if;
end;
!!
delimiter ;

delimiter !!
create trigger id_message
before insert on message
for each row
begin
	declare max_id integer;
	select max(m_id) into max_id from message;
	if max_id is null
    then
		set new.m_id = 1;
	else
		set new.m_id = max_id + 1;
	end if;
end;
!!
delimiter ;



















