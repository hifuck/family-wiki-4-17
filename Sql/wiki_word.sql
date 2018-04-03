/**
词条表
*/
create table IF NOT EXISTS wiki_word  (
    id int not null primary key auto_increment comment '主键',
    word varchar(40) not null comment '词条',
    content text not null comment '词条内容',
    type int not null comment '词条类型',
    template int not null comment '词条模板编号',
    version int not null comment '版本号',
    isDelete tinyint(1) not null comment '词条是否被删除',
    createTime DATETIME not null comment '词条的创建时间',
    updateTime DATETIME not null comment '词条的更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;

create table IF NOT EXISTS wiki_word_verify (
    id int not null primary key auto_increment comment '主键',
    word varchar(40) not null comment '词条',
    content text not null comment '词条内容',
    type int not null comment '词条类型',
    template int not null comment '词条模板编号',
    version int not null comment '版本号',
    isDelete tinyint(1) not null comment '词条是否被删除',
    isVerify tinyint(1) not null comment '词条是否通过审核',
    createTime DATETIME not null comment '词条的创建时间',
    updateTime DATETIME not null comment '词条的更新时间' 
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;

create table  IF NOT EXISTS  wiki_author (
    id int not null primary key auto_increment comment '主键',
    word varchar(40) not null comment '词条',
    author varchar(45) not null comment '词条作者',
    createTime DATETIME not null comment '创建时间',
    updateTime DATETIME not null comment '更新时间',
    index index_word (word),
    index index_author (author)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;

create table IF NOT EXISTS wiki_history (
    id int not null primary key auto_increment comment '主键',
    word varchar(40) not null comment '词条',
    content text not null comment '内容',
    type int not null comment '词条类型',
    template int not null comment '词条模板编号',
    version int not null comment '版本号',
    author varchar(45) not null comment '作者用户名',
    createTime DATETIME not null comment '该记录的创建时间',
    updateTime DATETIME not null comment '该记录的更新时间',
    index index_word (word)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;


/* 词条分类 */
create table IF NOT EXISTS wiki_word_type (
    id int not null primary key auto_increment comment '主键',
    type varchar(20) not null comment '类型',
    parentId int not null comment '父类型',
    depth int not null comment '类型深度',
    createTime DATETIME not null comment '创建时间',
    updateTime DATETIME not null comment '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;

/* 模板 */

create table IF NOT EXISTS wiki_word_template (
    id int not null primary key auto_increment comment '主键',
    typeId int not null comment '类型Id',
    name varchar(45) not null comment '模板名称',
    content text not null comment '模板的内容',
    createTime DATETIME not null comment '创建时间',
    updateTime DATETIME not null comment '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 auto_increment=1;