<?php

namespace App\Media;

use App\Models\Exercise;
use App\Models\Location;
use App\Models\TrainingPlan;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class UserPathGenerator implements PathGenerator
{
    public function getPath(Media $media): string
    {
        return $this->getUserFolder($media) . $media->id . '/';
    }

    public function getPathForConversions(Media $media): string
    {
        return $this->getUserFolder($media) . $media->id . '/conversions/';
    }

    public function getPathForResponsiveImages(Media $media): string
    {
        return $this->getUserFolder($media) . $media->id . '/responsive/';
    }

    private function getUserFolder(Media $media): string
    {
        $model = $media->model;

        $userId = match (true) {
            $model instanceof Location     => $model->user_id,
            $model instanceof TrainingPlan => $model->location->user_id,
            $model instanceof Exercise     => $model->trainingPlan->location->user_id,
            default                        => 'shared',
        };

        return "users/{$userId}/";
    }
}
