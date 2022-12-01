<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateRoutines extends Migration {

    public function up() {
        DB::unprepared('CREATE PROCEDURE `copyTopic`(IN `p_topic_id` INT, IN `new_meeting_id` INT, IN `copy_meeting_id` INT, IN `series` INT)
    NO SQL
BEGIN
     DECLARE new_parent_id INT;
    DECLARE new_child_id INT;
    DECLARE new_child_id_l3 INT;
    DECLARE series_l2 INT Default 0;
    DECLARE series_l3 INT Default 0;
    DECLARE old_topic_val INT;
    DECLARE no_more_rows BOOLEAN Default false;
    DECLARE _topic_id INT;
    DECLARE _topic_level INT;
    DECLARE _topic_text VARCHAR(1024);
    DECLARE _wid INT;
    DECLARE _p_id INT;
    DECLARE __p_id INT;
    DECLARE __topic_id INT;
    DECLARE __topic_level INT;
    DECLARE __topic_text VARCHAR(1024);
    DECLARE __wid INT;
        
    DECLARE cursor1 CURSOR FOR
        SELECT id,level,topic_title,workshop_id,parent_id
        FROM topics 
        WHERE parent_id = p_topic_id AND reuse = 1 AND meeting_id = copy_meeting_id;
    DECLARE cursor2 CURSOR FOR
        SELECT id,level,topic_title,workshop_id,parent_id
        FROM topics 
        WHERE grand_parent_id = p_topic_id AND reuse = 1 AND meeting_id = copy_meeting_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET no_more_rows := TRUE;
    INSERT INTO topics(id,parent_id,grand_parent_id,level,topic_title,meeting_id,discussion,decision,reuse,workshop_id,list_order) SELECT NULL,NULL,NULL,level,topic_title,new_meeting_id,NULL,NULL,1,workshop_id,NULL FROM topics WHERE id=p_topic_id AND reuse = 1  AND meeting_id = copy_meeting_id;
    SET new_parent_id:=LAST_INSERT_ID();
  
 OPEN cursor1;
        loop1: LOOP
            FETCH cursor1 INTO _topic_id,_topic_level,_topic_text,_wid,_p_id;
            IF no_more_rows THEN
                CLOSE cursor1;
                LEAVE loop1;
            END IF;
            SET series_l2 := series_l2 + 1;
            INSERT INTO topics(id,parent_id,grand_parent_id,level,topic_title,meeting_id,discussion,decision,reuse,workshop_id) VALUES (NULL,new_parent_id,NULL,_topic_level,_topic_text,new_meeting_id,NULL,NULL,1,_wid);
            SET new_child_id := LAST_INSERT_ID();
            SET old_topic_val := _topic_id;
            OPEN cursor2;
            loop2: LOOP
                FETCH cursor2 INTO __topic_id,__topic_level,__topic_text,__wid,__p_id;
                IF no_more_rows THEN
                    SET no_more_rows := FALSE;
                    CLOSE cursor2;
                    LEAVE loop2;
                END IF;
                SET series_l3 := series_l3 + 1;
                IF __p_id = _topic_id THEN
                INSERT INTO topics(id,parent_id,grand_parent_id,level,topic_title,meeting_id,discussion,decision,reuse,workshop_id) VALUES (NULL,new_child_id,new_parent_id,__topic_level,__topic_text,new_meeting_id,NULL,NULL,1,__wid);
                END IF;
            END LOOP loop2;
            SET series_l3 := 0;
        END LOOP loop1;
END');
    }

    public function down() {
        DB::unprepared('DROP PROCEDURE IF EXISTS `copyTopic`');
    }

}
