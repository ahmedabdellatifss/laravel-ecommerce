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


        ////////   ...Store...   ////////

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
            if($request->has('photo')) { //#17

                $filePath = uploadImage('maincategories' , $request->photo);

            }

            DB::beginTransaction();

                $default_category_id = MainCategory::insertGetId([ // it's the same method created

                    'translation_lang' => $default_category['abbr'],
                    'translation_of' => 0,
                    'name' => $default_category['name'],
                    'slug' => $default_category['name'],
                    'photo' => $filePath,
                ]);

                $categories = $main_categories->filter(function($value , $key) { //di hatgib kol el categories eli mesh arabic
                    return $value['abbr'] !== get_default_lang();
                });

                if (isset($categories) && $categories->count())
                {
                    $categories_arr = [];
                    foreach($categories as $category )
                    {
                        $categories_arr[] = [
                            'translation_lang' => $category['abbr'],
                            'translation_of' => $default_category_id,
                            'name' => $category['name'],
                            'slug' => $category['name'],
                            'photo' => $filePath
                        ];
                    }
                    MainCategory::insert($categories_arr);
                }

            DB::commit();

            return redirect()->route('admin.maincategories')->with(['success'  => 'تم الحفظ بنجاح']);

        }catch (\Exception $ex){

            DB::rollback();

            return redirect()->route('admin.maincategories')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }

    }



    ///////  ...Edit... ////////

    public function edit ($mainCat_id)
    {

        $mainCategory = MainCategory::selection()->find($mainCat_id);  //#20

        if (!$mainCategory)
            return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);


        return view('admin.maincategories.edit' , compact('mainCategory'));
    }



    ////////  ...Update...  ///////////

    public function update($mainCat_id, MainCategoryRequest $request)
    {


        $main_category = MainCategory::find($mainCat_id);

        if(!$main_category)
        return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);

        // if main_category is exist in database we will update database

        $category = array_values($request -> category)[0];  // #20
        MainCategory::where('id' , $mainCat_id)->update([
            'name' => $category['name']
        ]);

        return redirect()->route('admin.maincategories')->with(['success'  => 'تم التحديث بنجاح']);
    }
}
