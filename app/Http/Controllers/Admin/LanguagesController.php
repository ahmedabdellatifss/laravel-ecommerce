<?php

namespace App\Http\Controllers\Admin;
use App\Models\Language;
use App\Http\Controllers\Controller;
use App\Http\Requests\LanguageRequest;
use Exception;
use Illuminate\Http\Request;

class LanguagesController extends Controller
{
    public function index () {
        $languages = Language::select()->paginate(PAGINATION_COUNT);
        return view('admin.languages.index' , compact('languages'));
    }

    public function create () {
        return view('admin.languages.create');
    }

    public function store (LanguageRequest $request)
    {
        try{
            Language::create($request->except(['_token']));
            return redirect()->route('admin.languages')->with(['success' => 'تم تسجيل اللغه اللغة  ']);
        }catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }

    }

    public function edit($id) {

        $language = Language::select()->find($id);

        if(! $language) {

            return redirect()->route('admin.languages')->with(['error'=>'هذه اللغة غير موجوده']);

        }

            return view('admin.languages.edit' , compact('language'));

    }


    public function update($id , LanguageRequest $request) {

        try{
            $language = Language::find($id);
            if(! $language) {
                return redirect()->route('admin.languages.edit' , $id)->with(['error'=>'هذه اللغة غير موجوده']);
            }
            if(!$request ->has('active'))
                $request ->request->add(['active'=>0]);
            //  Update in database
            $language-> update($request -> except('_token'));
            return redirect()->route('admin.languages')->with(['success' => 'تم تحديث اللغه بنجاح  ']);

        }catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }

    }

    public function destroy($id) {
        try{
            $language = Language::find($id);
            if(! $language) {
                return redirect()->route('admin.languages' , $id)->with(['error'=>'هذه اللغة غير موجوده']);
            }
            //  Update in database
            $language-> delete();
            return redirect()->route('admin.languages')->with(['success' => 'تم حذف  اللغه بنجاح  ']);

        }catch (\Exception $ex) {
            return redirect()->route('admin.languages')->with(['error' => 'هناك خطا ما يرجي المحاوله فيما بعد']);
        }
    }
}
