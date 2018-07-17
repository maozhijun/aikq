<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/3
 * Time: 11:54
 */

namespace App\Models\Subject;


use Illuminate\Database\Eloquent\Model;

/**
 * 联赛专题
 * Class SubjectLeague
 * @package App\Models\CMS\Subject
 */
class SubjectLeague extends Model
{
//    protected $connection = "match";
    const kSportFootball = 1, kSportBasketball = 2;//1：足球，2：篮球
    const kStatusShow = 1, kStatusHide = 2;//1：显示，2：隐藏

    /**
     * 获取所有显示的专题联赛
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public static function getAllLeagues() {
        $query = self::query()->where('status', self::kStatusShow);
        $query->orderBy('od');//->orderBy('sport');
        return $query->get();
    }

    /**
     * 获取名称
     * @return string
     */
    public function getName() {
        $sportCn = $this->sportCn();
        $sportCn = $sportCn == '' ? '' : $sportCn . '：';
        return $sportCn . $this->name;
    }

    /**
     * 类型中文
     * @return string
     */
    public function sportCn() {
        $sport = $this->sport;
        $sportCn = '';
        if ($sport == self::kSportFootball) {
            $sportCn = '足球';
        } else if ($sport == self::kSportBasketball) {
            $sportCn = '篮球';
        }
        return $sportCn;
    }

    /**
     *
     * @return mixed
     */
    public function contentHtml() {
        $content = $this->content;
        if (!empty($content)) {
            $content = str_replace(' ', '&nbsp;', $content);
            $content = str_replace("\n", '<br/>', $content);
        }
        return $content;
    }

}