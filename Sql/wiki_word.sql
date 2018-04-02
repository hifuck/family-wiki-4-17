/**
词条表
*/

select database family_wiki;

create table IF NOT EXISTS wiki_word  (
    word varchar(40) not null primary key comment '主键，词条',
    content text not null comment '词条内容',
    type int not null comment '词条类型',
    template int not null comment '词条模板编号',
    version int not null comment '版本号',
    isDelete tinyint(1) not null comment '词条是否被删除',
    isVerify tinyint(1) not null comment '词条是否通过审核',
    createTime DATETIME not null comment '词条的创建时间',
    updateTime DATETIME not null comment '词条的更新时间',
    verifyTime DATETIME null comment '词条的审核时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

create table  IF NOT EXISTS  wiki_author (
    word varchar(40) not null comment '词条',
    author varchar(45) not null comment '词条作者',
    createTime DATETIME not null comment '创建时间',
    updateTime DATETIME not null comment '更新时间',
    index index_word (word),
    index index_author (author),
    CONSTRAINT pk_word_author PRIMARY KEY (word,author)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

create table IF NOT EXISTS wiki_history (
    word varchar(40) not null comment '词条',
    content text not null comment '内容',
    type int not null comment '词条类型',
    template int not null comment '词条模板编号',
    version int not null comment '版本号',
    author varchar(45) not null comment '作者用户名',
    createTime DATETIME not null comment '该记录的创建时间',
    updateTime DATETIME not null comment '该记录的更新时间',
    index index_word (word),
    CONSTRAINT pk_word_version PRIMARY KEY (word,version)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;