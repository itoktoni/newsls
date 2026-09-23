<?php

namespace App\Concerns;

trait NormalizeInputTrait
{
    private function normalizeInput(array $input, array $map): array
    {
        $data = [];
        foreach ($map as $from => $to) {
            if (isset($input[$from]) && ! isset($data[$to])) {
                $data[$to] = $input[$from];
            }
        }

        return $data;
    }

    private function normalizeAnakInput(array $input): array
    {
        $data = $this->normalizeInput($input, [
            'nama' => 'anak_nama', 'anak_nama' => 'anak_nama',
            'gender' => 'anak_gender', 'anak_gender' => 'anak_gender',
            'agama' => 'anak_agama', 'anak_agama' => 'anak_agama',
            'umur' => 'anak_umur', 'anak_umur' => 'anak_umur',
            'tanggal_lahir' => 'anak_tanggal_lahir', 'anak_tanggal_lahir' => 'anak_tanggal_lahir', 'tanggal' => 'anak_tanggal_lahir',
            'bulan_lahir' => 'anak_bulan_lahir', 'anak_bulan_lahir' => 'anak_bulan_lahir', 'bulan' => 'anak_bulan_lahir',
            'tahun_lahir' => 'anak_tahun_lahir', 'anak_tahun_lahir' => 'anak_tahun_lahir', 'tahun' => 'anak_tahun_lahir',
            'emoji' => 'anak_emoji', 'anak_emoji' => 'anak_emoji',
            'avatar' => 'anak_avatar', 'anak_avatar' => 'anak_avatar',
            'settings' => 'anak_settings', 'anak_settings' => 'anak_settings',
        ]);

        foreach (['anak_umur', 'anak_tanggal_lahir', 'anak_bulan_lahir', 'anak_tahun_lahir'] as $intField) {
            if (isset($data[$intField]) && $data[$intField] !== '' && $data[$intField] !== null) {
                $data[$intField] = (int) $data[$intField];
            }
        }

        return $data;
    }

    private function normalizeSkillInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'key' => 'skill_key', 'skill_key' => 'skill_key',
            'emoji' => 'skill_emoji', 'skill_emoji' => 'skill_emoji',
            'title' => 'skill_title', 'skill_title' => 'skill_title',
            'pilar' => 'skill_pilar', 'skill_pilar' => 'skill_pilar',
            'progress' => 'skill_progress', 'skill_progress' => 'skill_progress',
            'color' => 'skill_color', 'skill_color' => 'skill_color',
        ]);
    }

    private function normalizeActivityInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'skill_key' => 'skill_key',
            'title' => 'skill_activity_title', 'skill_activity_title' => 'skill_activity_title',
            'emoji' => 'skill_activity_emoji', 'skill_activity_emoji' => 'skill_activity_emoji',
            'feature' => 'skill_activity_feature', 'skill_activity_feature' => 'skill_activity_feature',
            'date' => 'skill_activity_date', 'skill_activity_date' => 'skill_activity_date',
        ]);
    }

    private function normalizeCompletedSkillInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'key' => 'completed_skill_key', 'completed_skill_key' => 'completed_skill_key',
            'emoji' => 'completed_skill_emoji', 'completed_skill_emoji' => 'completed_skill_emoji',
            'title' => 'completed_skill_title', 'completed_skill_title' => 'completed_skill_title',
            'pilar' => 'completed_skill_pilar', 'completed_skill_pilar' => 'completed_skill_pilar',
            'color' => 'completed_skill_color', 'completed_skill_color' => 'completed_skill_color',
            'completed_at' => 'completed_skill_completed_at', 'completed_skill_completed_at' => 'completed_skill_completed_at', 'completedAt' => 'completed_skill_completed_at',
        ]);
    }

    private function normalizeChallengeInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'category' => 'challenge_category', 'challenge_category' => 'challenge_category',
            'title' => 'challenge_title', 'challenge_title' => 'challenge_title',
            'emoji' => 'challenge_emoji', 'challenge_emoji' => 'challenge_emoji',
            'points' => 'challenge_points', 'challenge_points' => 'challenge_points',
            'maxPoints' => 'challenge_maxPoints',
            'status' => 'challenge_status', 'challenge_status' => 'challenge_status',
            'date' => 'challenge_date', 'challenge_date' => 'challenge_date',
            'meta' => 'challenge_meta', 'challenge_meta' => 'challenge_meta',
        ]);
    }

    private function normalizeChallengeHistoryInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'category' => 'challenge_history_category', 'challenge_history_category' => 'challenge_history_category',
            'title' => 'challenge_history_title', 'challenge_history_title' => 'challenge_history_title',
            'date' => 'challenge_history_date', 'challenge_history_date' => 'challenge_history_date',
            'meta' => 'challenge_history_meta', 'challenge_history_meta' => 'challenge_history_meta',
        ]);
    }

    private function normalizeChecklistInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'title' => 'checklist_title', 'checklist_title' => 'checklist_title',
            'items' => 'checklist_items', 'checklist_items' => 'checklist_items',
            'date' => 'checklist_date', 'checklist_date' => 'checklist_date',
        ]);
    }

    private function normalizeScheduleInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'label' => 'schedule_label', 'schedule_label' => 'schedule_label',
            'time' => 'schedule_time', 'schedule_time' => 'schedule_time',
            'done' => 'schedule_done', 'schedule_done' => 'schedule_done',
            'date' => 'schedule_date', 'schedule_date' => 'schedule_date',
        ]);
    }

    private function normalizeWorksheetInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'type' => 'worksheet_type', 'worksheet_type' => 'worksheet_type',
            'data' => 'worksheet_data', 'worksheet_data' => 'worksheet_data',
            'date' => 'worksheet_date', 'worksheet_date' => 'worksheet_date',
        ]);
    }

    private function normalizeEvaluationInput(array $input): array
    {
        return $this->normalizeInput($input, [
            'skill_key' => 'evaluation_skill_key', 'evaluation_skill_key' => 'evaluation_skill_key',
            'skill_title' => 'evaluation_skill_title', 'evaluation_skill_title' => 'evaluation_skill_title',
            'pilar' => 'evaluation_pilar', 'evaluation_pilar' => 'evaluation_pilar',
            'points' => 'evaluation_points', 'evaluation_points' => 'evaluation_points',
            'max_points' => 'evaluation_max_points', 'evaluation_max_points' => 'evaluation_max_points',
            'notes' => 'evaluation_notes', 'evaluation_notes' => 'evaluation_notes',
        ]);
    }
}
