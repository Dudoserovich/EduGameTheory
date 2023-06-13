<?php

namespace App\Previewer;

use App\Entity\UserAchievement;
use JetBrains\PhpStorm\ArrayShape;

class UserAchievementPreviewer
{
    private UserPreviewer $userPreviewer;
    private AchievementPreviewer $achievementPreviewer;

    public function __construct(
        UserPreviewer $userPreviewer,
        AchievementPreviewer $achievementPreviewer
    )
    {
        $this->userPreviewer = $userPreviewer;
        $this->achievementPreviewer = $achievementPreviewer;
    }

    #[ArrayShape([
        "id" => "int",
        "achievement" => [
            "id" => "int",
            "name" => "string",
            "description" => "string",
            "image_href" => "string"
        ],
        "user" => [
            "id" => "int",
            "full_name" => "string",
            "login" => "string",
            "roles" => "string[]",
            "email" => "string",
            "avatar" => "string",
        ],
        "total_score" => "int",
        "achievement_date" => "datetime"
    ])]
    public function previewAll(UserAchievement $userAchievement): array
    {
        return [
            "id" => $userAchievement->getId(),
            "achievement" => $this->achievementPreviewer->preview($userAchievement->getAchievement()),
            "user" => $this->userPreviewer->preview($userAchievement->getUser()),
            "total_score" => $userAchievement->getTotalScore(),
            "achievement_date" => $userAchievement->getAchievementDate()
        ];
    }

    #[ArrayShape([
        "id" => "int",
        "achievement" => [
            "id" => "int",
            "name" => "string",
            "description" => "string",
            "image_href" => "string"
        ],
        "progress" => "array",
        "achievement_date" => "datetime"
    ])]
    public function previewWithoutUser(UserAchievement $userAchievement): array
    {
        $achievement = $userAchievement->getAchievement();
        $progress = ["current_score" => $userAchievement->getTotalScore(), "need_score" => $achievement->getNeedScore()];
        return [
            "id" => $userAchievement->getId(),
            "achievement" => $this->achievementPreviewer->preview($achievement),
            "progress" => $progress,
            "achievement_date" => $userAchievement->getAchievementDate()
        ];
    }

}