<?php

use Illuminate\Database\Migrations\Migration;

class CreateConversatonsTableTriggers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        $this->dropTriggers();

        $this->createTriggerForConversations();

    }

    private function createTriggerForConversations() {

        DB::unprepared('
        CREATE TRIGGER event_conversation_logs
        AFTER INSERT ON kct_conversations
        FOR EACH ROW

        BEGIN
            IF((SELECT event_uuid FROM events WHERE event_uuid =(SELECT event_uuid FROM event_spaces WHERE space_uuid = NEW.space_uuid) AND start_time <= NEW.created_at) IS NOT NULL) THEN
                INSERT INTO log_event_conversations
                    (rec_uuid,space_uuid,convo_uuid,convo_start)
                VALUES (
                    (SELECT recurrence_uuid FROM event_single_recurrences
                        where event_uuid = (SELECT event_uuid FROM event_spaces WHERE space_uuid = NEW.space_uuid)
                        ORDER BY recurrence_count DESC LIMIT 1
                    ),
                    NEW.space_uuid,
                    NEW.uuid,
                    NOW()
                );
            END IF;
        END

        ');

        DB::unprepared('
            CREATE TRIGGER event_conversation_end_logs
            AFTER UPDATE ON kct_conversations

            FOR EACH ROW

            BEGIN
            IF (NEW.end_at IS NOT NULL) THEN
                UPDATE log_event_conversations
                SET convo_end      = NEW.end_at
                WHERE convo_uuid = OLD.uuid;
            END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER event_conversation_sub_state_trigger
            AFTER INSERT ON kct_conversation_users

            FOR EACH ROW
            BEGIN
            IF((SELECT id from log_event_conversations WHERE convo_uuid = NEW.conversation_uuid LIMIT 1) IS NOT NULL) THEN
                INSERT INTO log_convo_sub_state(
                    convo_log_id,
                    users_count,
                    start_time
                )
                VALUES(
                    (SELECT id from log_event_conversations WHERE convo_uuid = NEW.conversation_uuid),
                    (SELECT COUNT(user_id) as user_count FROM `kct_conversation_users` WHERE conversation_uuid = NEW.conversation_uuid AND leave_at IS NULL),
                    NOW()
                );
            END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER event_conversation_end_sub_state_trigger
            BEFORE INSERT ON kct_conversation_users
            FOR EACH ROW
            BEGIN
                UPDATE log_convo_sub_state
                SET
                    end_time        = NOW(),
                    duration        = TIMESTAMPDIFF(SECOND,(SELECT start_time from log_convo_sub_state where convo_log_id = (SELECT id from log_event_conversations WHERE convo_uuid = New.conversation_uuid  ORDER BY id DESC LIMIT 1) ORDER BY id DESC LIMIT 1),NOW())
                WHERE convo_log_id = (SELECT id from log_event_conversations WHERE convo_uuid = NEW.conversation_uuid ORDER BY ID DESC LIMIT 1)
                ORDER BY ID DESC LIMIT 1;
            END
        ');


        DB::unprepared('
            CREATE TRIGGER event_user_conversation_end_sub_state_trigger
            AFTER UPDATE ON kct_conversation_users
            FOR EACH ROW
            BEGIN
                IF(NEW.leave_at IS NOT NULL) THEN
                    BEGIN
                        UPDATE log_convo_sub_state
                        SET
                            end_time    = NOW(),
                            duration    = TIMESTAMPDIFF(SECOND,(SELECT start_time from log_convo_sub_state where convo_log_id = (SELECT id from log_event_conversations WHERE convo_uuid = New.conversation_uuid  ORDER BY id DESC LIMIT 1) ORDER BY id DESC LIMIT 1),NOW())
                        WHERE convo_log_id = (SELECT id from log_event_conversations WHERE convo_uuid = NEW.conversation_uuid ORDER BY ID DESC LIMIT 1)
                        ORDER BY ID DESC LIMIT 1;
                    END;

                     BEGIN
                            IF((SELECT COUNT(user_id) as user_count FROM `kct_conversation_users` WHERE conversation_uuid = (SELECT uuid from kct_conversations WHERE uuid= NEW.conversation_uuid AND created_at > (SELECT start_time from events where event_uuid = (SELECT event_uuid from event_spaces where space_uuid = kct_conversations.space_uuid))) AND leave_at IS NULL) > 1) THEN
                                 INSERT INTO log_convo_sub_state(
                                        convo_log_id,
                                        users_count,
                                        start_time
                                  )
                                VALUES(
                                    (SELECT id from log_event_conversations WHERE convo_uuid = New.conversation_uuid),
                                    (SELECT COUNT(user_id) as user_count FROM `kct_conversation_users` WHERE conversation_uuid = NEW.conversation_uuid AND leave_at IS NULL),
                                    NOW()
                                );
                            END IF;
                     END;
                END IF;
            END

        ');


    }


    private function dropTriggers() {
        DB::unprepared('DROP TRIGGER IF EXISTS event_conversation_logs');
        DB::unprepared('DROP TRIGGER IF EXISTS event_conversation_end_logs');
        DB::unprepared('DROP TRIGGER IF EXISTS event_conversation_sub_state_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS event_conversation_end_sub_state_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS event_user_conversation_end_sub_state_trigger');
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->dropTriggers();
    }
}
