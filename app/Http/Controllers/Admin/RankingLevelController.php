<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RankingLevelRequest;
use App\Models\BlogTag;
use App\Models\RankingLevel;
use App\Tools\Repositories\Crud;
use App\Traits\General;
use App\Traits\ImageSaveTrait;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Storage;

class RankingLevelController extends Controller
{
    use General, ImageSaveTrait;
    protected $model;

    public function __construct(RankingLevel $level)
    {
        $this->model = new Crud($level);
    }

    public function index()
    {
        if (!Auth::user()->can('ranking_level')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Manage Ranking Level';
        $data['navRankingActiveClass'] = "mm-active";
        $data['subNavRankingActiveClass'] = "mm-active";
        $data['levels'] = $this->model->getOrderById('ASC', 25);
        return view('admin.ranking.index', $data);
    }

    public function edit($uuid)
    {
        if (!Auth::user()->can('ranking_level')) {
            abort('403');
        } // end permission checking

        $data['title'] = 'Edit Ranking Level';
        $data['navRankingActiveClass'] = "mm-active";
        $data['subNavRankingActiveClass'] = "mm-active";
        $data['level'] = $this->model->getRecordByUuid($uuid);
        return view('admin.ranking.edit', $data);
    }

    public function store(RankingLevelRequest $request)
    {
        if (!Auth::user()->can('ranking_level')) {
            abort('403');
        } // end permission checking

        $ranking = new RankingLevel();
        $ranking->name = $request->name;
        $ranking->earning = $request->earning;
        $ranking->student = $request->student;
        $ranking->serial_no = $request->serial_no;

        if ($request->badge_image){
            $ranking->badge_image = $this->saveImage('ranking_level', $request->badge_image, null, null);
        }

        $ranking->save();

        $this->showToastrMessage('success', 'Created Successful');
        return redirect()->back();
    }

    public function update(Request $request, $uuid)
    {
        if (!Auth::user()->can('ranking_level')) {
            abort('403');
        } // end permission checking

        $ranking = $this->model->getRecordByUuid($uuid);

        $request->validate([
            'name' => 'required|max:255|unique:ranking_levels,name,' . $ranking->id,
            'serial_no' => 'required|max:20|unique:ranking_levels,serial_no,' . $ranking->id,
            'earning' => 'required|min:0',
            'student' => 'required|min:0',
            'badge_image' => 'mimes:png,jpg|file|dimensions:min_width=86,min_height=66,max_width=86,max_height=66|max:1024'
        ]);

        $ranking->name = $request->name;
        $ranking->earning = $request->earning;
        $ranking->student = $request->student;
        $ranking->serial_no = $request->serial_no;

        if ($request->badge_image){
            $ranking->badge_image = $this->updateImage('ranking_level', $request->badge_image, $ranking->badge_image, 'null', 'null');
        }

        $ranking->save();

        $this->showToastrMessage('success', 'Updated Successful');
        return redirect()->route('ranking.index');
    }

    public function delete($uuid)
    {
        if (!Auth::user()->can('ranking_level')) {
            abort('403');
        } // end permission checking

        $ranking = $this->model->getRecordByUuid($uuid);
        $this->deleteFile($ranking->badge_image); // delete file from server
        $this->model->deleteByUuid($uuid);

        $this->showToastrMessage('error', 'Ranking Level has been deleted');
        return redirect()->back();
    }
}
