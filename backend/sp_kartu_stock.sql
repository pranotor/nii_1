DELIMITER $$

USE `nii`$$

DROP PROCEDURE IF EXISTS `sp_kartustock`$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_kartustock`(tahun INT,bulan INT,periode VARCHAR(10), rangetgl VARCHAR(30), nama VARCHAR(1000))
BEGIN
	DECLARE no_more_rows BOOLEAN DEFAULT FALSE;
	DECLARE it_id INT;
	DECLARE kp VARCHAR(20);
	DECLARE kb VARCHAR(20);
	DECLARE ur VARCHAR(1000);
	DECLARE curTemp CURSOR FOR SELECT item_id,kode_perk,kode_barang,uraian FROM ks_temp ;
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows = TRUE;
	SET @tbl_num := FLOOR(RAND()*(100));
	SET @tbl_temp := CONCAT('ks_temp');
	SET @tbl_final := CONCAT('ks_final');
	SET @drop_tbl_temp = CONCAT("DROP TEMPORARY TABLE IF EXISTS ",@tbl_temp);
	SET @drop_tbl_final = CONCAT("DROP TEMPORARY TABLE IF EXISTS ",@tbl_final);
	PREPARE stmt FROM @drop_tbl_temp;
	EXECUTE stmt;
	IF nama <> '' THEN
	   SET @where_nama = CONCAT(" AND i.kode_barang like '%",nama,"%' ");
	ELSE
	   SET @where_nama = " ";
	END IF;
	IF periode = "bl" THEN
	    SET @where_tgl = CONCAT(" AND YEAR(tgl)=",tahun," AND MONTH(tgl)=",bulan);
	    SET @where_tgl_pre = CONCAT(" AND YEAR(tgl)=",tahun," AND MONTH(tgl)<",bulan);
	    SET @tgl_saldo = CONCAT(tahun,"-",bulan,"-01");
	ELSEIF periode = "th" THEN
	    SET @where_tgl = CONCAT(" AND YEAR(tgl)=",tahun);
	    SET @tgl_saldo = CONCAT(tahun,"-",'01',"-01");
	ELSE
	   IF rangetgl <> '' THEN
		   SET @tgl_awal = SUBSTRING_INDEX(rangetgl, ',', 1);
		   SET @tgl_akhir = SUBSTRING_INDEX(rangetgl, ',', -1);
		   #set @where_range = concat(" AND tanggal >='",@tgl_awal,"' AND tanggal <='",@tgl_akhir,"' ");
		   SET @where_tgl = CONCAT(" AND tgl>='",@tgl_awal,"' AND tgl <='",@tgl_akhir,"' ");
		   SET @tgl_saldo = SUBDATE(@tgl_awal, INTERVAL 1 DAY);
		   IF MONTH(@tgl_saldo) <> MONTH(@tgl_awal) THEN
			SET @tgl_saldo = CONCAT(tahun,"-",MONTH(@tgl_awal),"-01");
		   END IF;
		   SET @where_tgl_pre = CONCAT(" AND tgl < '",@tgl_awal,"'");
	   ELSE
		   SET @where_tgl = " ";
	   END IF;
	END IF;
	SET @create_tbl_temp = CONCAT("CREATE TEMPORARY TABLE ",@tbl_temp," AS SELECT DISTINCT i.item_id,r.`kode_perk`,
			t.kode_barang,r.`nama_perk`,t.`uraian`,t.`satuan` FROM t_invent AS i
			INNER JOIN t_item AS t ON i.`item_id` = t.`id`
			INNER JOIN rekening AS r ON t.`kode_perk` = r.`kode_perk` WHERE 1=1 ",@where_nama," ORDER BY i.kode_barang");
	PREPARE stmt FROM @create_tbl_temp;
	EXECUTE stmt;
	PREPARE stmt FROM @drop_tbl_final;
	EXECUTE stmt;
	CREATE TEMPORARY TABLE `ks_final` SELECT * FROM `stock_template` WHERE 1 = 0;
	OPEN curTemp;
	ks_loop: LOOP
		FETCH NEXT FROM curTemp INTO it_id, kp, kb, ur;
		#select kp;
		IF no_more_rows THEN
			LEAVE ks_loop;
		END IF;
		INSERT INTO ks_final VALUES(kp,kb,ur,'','','','','');
		#insert saldo lalu (jika bl atau range)
		IF periode='bl' OR periode='rg' THEN
			SET @sql_insert_prev = CONCAT("insert into ks_final SELECT '-',kode_barang,'-','",@tgl_saldo,"','Saldo Periode Lalu',(SUM(IF(`status`='IN',qty,0)) - SUM(IF(`status`='OUT',qty,0))) AS tambah,
					0,'-' FROM t_invent where item_id=",it_id," ",@where_tgl_pre);
			#select @sql_insert_prev;
			PREPARE stmt FROM @sql_insert_prev;
			EXECUTE stmt;
		END IF;
		SET @sql_insert = CONCAT("insert into ks_final SELECT '-',kode_barang,'-',tgl,ref,IF(`status`='IN',qty,0)AS tambah,
				IF(`status`='OUT',qty,0)AS kurang,keterangan FROM t_invent where item_id=",it_id," ",@where_tgl," order by tgl");
		PREPARE stmt FROM @sql_insert;
		EXECUTE stmt;
	END LOOP;
	DELETE FROM ks_final WHERE kode_barang IS NULL;
	SELECT * FROM ks_final;
    END$$

DELIMITER ;
