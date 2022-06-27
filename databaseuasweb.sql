create table pasien(
    id_pasien int primary key,
    nama varchar(255),
    tempat_lahir varchar(255),
    tanggal_lahir date,
    tanggal_masuk date,
    alamat varchar(255),
    no_bpjs varchar(255),
    jenis_kelamin varchar(1),
    keluhan text,
    dokter int,
    resepsionis int,
    created_at datetime default CURRENT_TIMESTAMP
);

create table dokter(
    id_dokter int primary key,
    nama varchar(255),
    alamat varchar(255),
    spesialis varchar(255),
    ruangan varchar(255),
    jadwal varchar(255),
    jenis_kelamin varchar(1),
    no_telepon varchar(20),
    created_at datetime default CURRENT_TIMESTAMP
);
create table resepsionis(
    id int primary key,
    nama varchar(255),
    no_telepon varchar(20),
    jam varchar(255),
    jenis_kelamin varchar(1),
    alamat varchar(255),
    created_at datetime default CURRENT_TIMESTAMP
);

insert into dokter values (11111, "dr. Tono", "jl.empedu", "organ dalam", "urologi 1", "senin-jumat", "L", "082288211111", default);
insert into dokter values (11112, "dr. Eni", "jl.bronkitis", "tht", "tht 2", "rabu-minggu", "P", "082288211112", default);
insert into dokter values (11113, "dr. Tejo", "jl.asma", "orang dalam", "biologi 1", "sabtu-minggu", "L", "082288211113", default);

insert into resepsionis values (22221, "Caca", "08224422221", "07.00-13.00", "P", "jl.ni aja", default);
insert into resepsionis values (22222, "Cici", "08224422222", "13.00-19.00", "P", "jl.jalan", default);
insert into resepsionis values (22223, "Ilham", "08224422223", "19.00-01.00", "L", "jl.ditempat", default);

insert into pasien values (33331, "David", "Rawa Putih", "2002-01-01", "2022-01-01", "jl.lurus", "989073", "L", "keuangan", 11111, 22222, default);
insert into pasien values (33332, "Rey", "Rawa Hitam", "1999-01-02", "2022-01-02", "jl.pertigaan", "989071", "L", "setres", 11113, 22221, default);
insert into pasien values (33333, "Gea", "Rawa Pening", "2000-01-03", "2022-01-03", "jl.perempatan", "Gak Ada", "P", "imun turun", 11112, 22223, default);