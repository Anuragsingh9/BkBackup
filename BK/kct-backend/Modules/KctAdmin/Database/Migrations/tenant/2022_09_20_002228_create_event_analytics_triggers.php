<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateEventAnalyticsTriggers extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {

        $this->dropTriggers();

        $this->createTriggerForAnytime();
        $this->createTriggerForDuringEvent();


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        $this->dropTriggers();
    }

    private function dropTriggers() {
        DB::unprepared('DROP TRIGGER IF EXISTS event_create_log');
        DB::unprepared('DROP TRIGGER IF EXISTS pilot_content_update');
        DB::unprepared('DROP TRIGGER IF EXISTS event_conversation_log_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS space_host_join_log');
        DB::unprepared('DROP TRIGGER IF EXISTS event_join_log_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS event_reg_log_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS event_user_remove_log_trigger');
        DB::unprepared('DROP TRIGGER IF EXISTS logs_event_attendee_count');
    }


    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description These triggers can be triggered even without the event is started
     * @example when user register in event we need to mark the registration count and event is not required to be live
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function createTriggerForAnytime() {
        $this->createTriggerForEventSingleRecurrenceTable();
    }

    /**
     * -----------------------------------------------------------------------------------------------------------------
     * @description These triggers must only trigger during the event only
     * -----------------------------------------------------------------------------------------------------------------
     */
    private function createTriggerForDuringEvent() {
        $this->createTriggerForEventJoinReports();
        $this->createTriggerForLogEventContent();
        $this->createTriggerForConversations();
        $this->createTriggerForEventUsers();
    }

    private function createTriggerForEventSingleRecurrenceTable() {
        DB::unprepared('
            CREATE TRIGGER event_create_log
            AFTER INSERT ON event_single_recurrences
            FOR EACH ROW

            BEGIN

            INSERT INTO log_event_action_counts (
                group_id,
                recurrence_uuid,
                created_at
            )
            VALUES (
                (select group_id from group_events where event_uuid = NEW.event_uuid),
                NEW.recurrence_uuid,
                NOW()
            );
            END
        ');
    }

    private function createTriggerForEventUsers () {
        // no need to check for time as registration can done before event and we need to count the registration
        // even if the event is not live
        DB::unprepared('
            CREATE TRIGGER event_join_log_trigger
                AFTER INSERT
                ON
                    event_users
                FOR EACH ROW
            BEGIN
                IF(NEW.is_joined_after_reg = 1 ) THEN
                UPDATE log_event_action_counts
                SET reg_count      = log_event_action_counts.reg_count
                WHERE recurrence_uuid =
                      (
                        SELECT recurrence_uuid
                        FROM event_single_recurrences
                        WHERE event_uuid = NEW.event_uuid
                        ORDER BY recurrence_count DESC LIMIT 1
                       );
            END IF;
            END
        ');

        // no need to check for time as registration can done before event and we need to count the registration
        // even if the event is not live
        DB::unprepared('
            CREATE TRIGGER event_reg_log_trigger
                AFTER UPDATE
                ON
                    event_users
                FOR EACH ROW
            BEGIN
                IF(OLD.is_joined_after_reg = 0 AND NEW.is_joined_after_reg = 1 ) THEN
                UPDATE log_event_action_counts
                SET reg_count      = log_event_action_counts.reg_count + 1
                WHERE recurrence_uuid =
                      (SELECT recurrence_uuid
                       FROM event_single_recurrences
                       WHERE event_uuid = NEW.event_uuid
                       ORDER BY recurrence_count DESC LIMIT 1
                       );
            END IF;
            END
        ');
    }

    private function createTriggerForEventJoinReports() {
        // no need to put the time check as in rehearsal mode we are not marking (inserting) the record so trigger will
        // also not trigger in rehearsal mode
        DB::unprepared('
            CREATE TRIGGER `logs_event_attendee_count`
            AFTER INSERT
            ON `event_user_join_reports`
            FOR EACH ROW

            BEGIN

                UPDATE log_event_action_counts
                SET attendee_count = (
                    SELECT COUNT(DISTINCT (user_id)) as user_count
                    FROM event_user_join_reports
                    WHERE
                        event_uuid = NEW.event_uuid
                        AND DATE (created_at) = (
                            SELECT
                            DATE(recurrence_date)
                            FROM event_single_recurrences
                            WHERE event_uuid = NEW.event_uuid
                            ORDER BY recurrence_count DESC LIMIT 1
                        )
                    )
                WHERE recurrence_uuid = (
                    SELECT recurrence_uuid
                    FROM event_single_recurrences
                    WHERE event_uuid = NEW.event_uuid
                    ORDER BY recurrence_count DESC LIMIT 1
                    );
            END
        ');
    }

    private function createTriggerForLogEventContent() {
        // no need to check for the event live as the logs are currently generating when event is live
        DB::unprepared('
            CREATE TRIGGER pilot_content_update
            AFTER INSERT ON log_event_contents
            FOR EACH ROW

            BEGIN
            IF (NEW.action = 3 )
            THEN
                UPDATE log_event_action_counts set p_image_count = log_event_action_counts.p_image_count + 1 where recurrence_uuid = NEW.recurrence_uuid;
                END IF;
            IF (NEW.action = 2 )
            THEN
                UPDATE log_event_action_counts set p_video_count = log_event_action_counts.p_video_count + 1 where recurrence_uuid = NEW.recurrence_uuid;
            END IF;
            END
        ');
    }

    private function createTriggerForConversations() {
        DB::unprepared('
            CREATE TRIGGER event_conversation_log_trigger
                AFTER INSERT
                ON
                    kct_conversations
                FOR EACH ROW
            BEGIN
            IF (NEW.is_host = 1) THEN
            UPDATE
                log_event_action_counts
            SET conv_count    = log_event_action_counts.conv_count + 1,
                sh_conv_count = log_event_action_counts.sh_conv_count + 1
            WHERE recurrence_uuid = (
                SELECT recurrence_uuid
                FROM event_single_recurrences
                WHERE event_uuid = (
                    SELECT event_uuid
                    FROM events
                    where event_uuid = (
                        SELECT event_uuid
                        FROM event_spaces
                        WHERE space_uuid = NEW.space_uuid
                    )
                      AND start_time <= NEW.created_at
                )
                ORDER BY recurrence_count DESC
                LIMIT
                1
                );
            END IF;
            IF (NEW.is_host = 0) THEN
            UPDATE
                log_event_action_counts
            SET conv_count = log_event_action_counts.conv_count + 1
            WHERE recurrence_uuid = (
                SELECT recurrence_uuid
                FROM event_single_recurrences
                WHERE event_uuid = (
                    SELECT event_uuid
                    FROM events
                    where event_uuid = (
                        SELECT event_uuid
                        FROM event_spaces
                        WHERE space_uuid = NEW.space_uuid
                    )
                      AND start_time <= NEW.created_at
                )
                ORDER BY recurrence_count DESC
                LIMIT
                1
                );
            END IF;
            END

        ');

        DB::unprepared('
            CREATE TRIGGER space_host_join_log
                AFTER UPDATE
                ON
                    kct_conversations
                FOR EACH ROW
            BEGIN
                IF(OLD.is_host = 0 AND NEW.is_host = 1 ) THEN
                UPDATE log_event_action_counts
                SET
                    sh_conv_count = log_event_action_counts.sh_conv_count + 1
                WHERE recurrence_uuid =
                      (SELECT recurrence_uuid
                       FROM event_single_recurrences
                       WHERE event_uuid = (
                            SELECT event_uuid
                            FROM events
                            where event_uuid = (
                                SELECT event_uuid
                                FROM event_spaces
                                WHERE space_uuid = NEW.space_uuid
                            )
                              AND start_time <= NEW.created_at
                        )
                       ORDER BY recurrence_count DESC LIMIT 1
                       );
                END IF;
            END
        ');
    }
}
