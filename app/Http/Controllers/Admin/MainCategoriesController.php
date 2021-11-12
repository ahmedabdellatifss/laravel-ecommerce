<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Exception;
use Illuminate\Http\Request;

use DB;



class MainCategoriesController extends Controller
{
    public function index ()
    {
        $default_lang = get_default_lang(); //#13

        $categories = MainCategory::where('translation_lang' , $default_lang)->selection()->get();

        return view('admin.mainCategories.index' , compact('categories'));
    }

    public function create()
    {
        return view('admin.mainCategories.create');

    }

    public function store(MainCategoryRequest $request)
    {
        try{
            // return $request;

            $main_categories = collect($request->category);  //#17

            $filter = $main_categories -> filter(function($value , $key) {
                return $value['abbr'] == get_default_lang();
            });


            $default_category = array_values($filter ->all()) [0];

            $filePath = '';
            if($request -> has('photo')) { //#17

                $filePath = uploadImage('maincategories' , $request ->photo);

            }

            DB::beginTransaction();

                $default_category_id = MainCategory::insertGetId([ // it's the same method created

                    'translation_lang' => $default_category['abbr'],
                    'translation_of' => 0,
                    'name' => $default_category['name'],
                    'slug' => $default_category['name'],
                    'photo' => $filePath,
                ]);

                $categories = $main_categories -> filter(function($value , $key) { //di hatgib kol el categories eli mesh arabic
                    return $value['abbr'] !== get_default_lang();
                });

                if (isset($categories) && $categories -> count())
                {
                    $categories_arr = [];
                    foreach($categories as $category )
                    {
                        $categories_arr[] = [
                            'translation_lang' => $category['abbr'],
                            'translation_of' => $default_category_id,
                        ];
                    }
                    MainCategory::insert($categories_arr);
                }

            DB::commit();

            return redirect()->route('admin.maincategories')->with(['success'  => 'تم الحفظ بنجاح']);

        }catch (\Exception $ex){

            DB::rollback();

            return redirect()->route('admin.maincategories')->with(['errors'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }

    }
}
