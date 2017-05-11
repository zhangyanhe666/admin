<?php
namespace Application\Controller;
use Application\Tool\Html;
use Application\Base\PublicController;

class VideomanageController extends PublicController
{
    private $needData = ['id', 'wkid', 'name', 'wktype', 'actors', 'directors', 'showtime', 'cover', 'area'];

    public function indexAction() {
        parent::indexAction();
        Html::addOption('merge', '合并',array(
            'onclick'=>'merge(__id);',
            'data-toggle'=>'modal',
            'data-target'=>'#myModal'
        ));
        $this->viewData()->setVariable('updateTime', date("Y-m-d H:i:s"));
    }

    public function mergeAction() {
        $id  =   $this->getRequest()->getQuery('id');
        if (($name = $this->selfModel('v_similar')->getItem($id)->name) == null) {
            return $this->responseError('没有数据');
        }
        $similarVideo = $this->selfModel('v_all')->where(array(new \Library\Db\Sql\Predicate\Like('search_word',$name."%"), 'black' => 0, 'is_delete' => 0))->columns($this->needData)->getAll()->toArray();
        return $this->responseSuccess($similarVideo);
    }

    public function mergePostAction() {
        $goalId  =   $this->getRequest()->getPost('goalId');
        $sourceId  =   $this->getRequest()->getPost('sourceId');
        if ($goalId == $sourceId || !$goalId || !$sourceId) {
            return $this->responseError('请选择不同的视频资源');
        }
        $goal = $this->getVideoInfoById($goalId);
        $validSource = $this->getVideoInfoByIds($sourceId);
        if (empty($goal) || empty($validSource)) {
            return $this->responseError('视频资源为空');
        }
        $validGoal = array_filter($goal);
        $video = array_diff_key($validSource, $validGoal);
        if (!empty($video) && !($this->selfModel('v_all')->update($video, ['id' => $goalId]))) {
            return $this->responseError('数据库合并失败');
        }
        $this->selfModel('v_all')->update(['black' => 1,'is_delete' => 1], "id in({$sourceId})");
        return $this->responseSuccess(['id' => $sourceId]);
    }

    private function getVideoInfoById($id) {
        $mergeData = ['wkid', 'moli_vid', 'dsm_vid', 'mango_vid', 'youku_vid', 'cibn_vid', 'tuzi_vid', 'taijie_vid', 'taijie_type', 'vst_vid', 'qq_vid', 'lizhi_vid', 'lizhi_tvQId', 'mifeng_vid', 'mifeng_pipelId', 'moli_cid', 'iqiyi_vid', 'sohu_vid'];
        return $this->selfModel('v_all')->columns($mergeData)->getItem($id)->toArray();
    }

    private function getVideoInfoByIds($id) {
        $mergeData = ['wkid', 'moli_vid', 'dsm_vid', 'mango_vid', 'youku_vid', 'cibn_vid', 'tuzi_vid', 'taijie_vid', 'taijie_type', 'vst_vid', 'qq_vid', 'lizhi_vid', 'lizhi_tvQId', 'mifeng_vid', 'mifeng_pipelId', 'moli_cid', 'iqiyi_vid', 'sohu_vid'];
        $source =  $this->selfModel('v_all')
            ->columns($mergeData)
            ->where("id in({$id})")
            ->getAll()
            ->toArray();
        $data = [];
        foreach ($source as $value) {
            $value = array_filter($value);
            $data = array_merge($data, $value);
        }
        return $data;
    }
}
