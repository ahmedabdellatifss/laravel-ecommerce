<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\MainCategoryRequest;
use App\Models\MainCategory;
use Exception;
use Illuminate\Http\Request;

use DB;
use Illuminate\Support\Str;



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
            //  Get Specific Categories and its translations
        $mainCategory = MainCategory::with('categories')->selection()->find($mainCat_id);  //#20

        if (!$mainCategory)
            return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);


        return view('admin.maincategories.edit' , compact('mainCategory'));
    }



    ////////  ...Update...  ///////////

    public function update($mainCat_id, MainCategoryRequest $request)
    {

        try{
            $main_category = MainCategory::find($mainCat_id);

            if(!$main_category)
            return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);

            // if main_category is exist in database we will update database

            $category = array_values($request -> category)[0];  // #20

            if(!$request ->has('category.0.active'))  // #21
                    $request ->request->add(['active'=>0]);
            else
                    $request ->request->add(['active'=>1]);

            // update name & active
            MainCategory::where('id' , $mainCat_id)->update([
                'name'   =>  $category['name'],
                'active' =>  $request->active,
            ]);


                // save image
            $filePath = $main_category -> photo;   //#21
            if($request->has('photo')) {
                $filePath = uploadImage('maincategories' , $request->photo);
                MainCategory::where('id' , $mainCat_id)->update([
                    'photo'  =>  $filePath,
                ]);
            }

            return redirect()->route('admin.maincategories')->with(['success'  => 'تم التحديث بنجاح']);

        }catch(\Exception $ex) {
            return redirect()->route('admin.maincategories')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);
        }

    }

    public function destroy($id)
    {

        try{

            $maincategory = MainCategory::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);

            $vendors = $maincategory->vendors(); //#36
            if(isset($vendors) && $vendors->count() > 0 ){
                return redirect()->route('admin.maincategories')->with(['error'  => 'لايمكن حذف هذا القسم ']);
            }

            // remove image from folder #37
            $image = Str::after($maincategory->photo , 'assets/');
            $image = base_path('assets/'.$image);
            unlink($image); //delete from folder


            // delete image from database
            $maincategory->delete();

            return redirect()->route('admin.maincategories')->with(['success'  => 'تم الحذف بنجاح']);

        }catch(\Exception $ex){

            return redirect()->route('admin.maincategories')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }
    }


    public function changeStatus($id) //#38
    {

        try{
            $maincategory = MainCategory::find($id);
            if (!$maincategory)
                return redirect()->route('admin.maincategories')->with(['error'  => ' هذا القسم غير موجود']);

            $status = $maincategory->active == 0 ? 1 : 0 ;

            $maincategory->update(['active' =>$status]);
            return redirect()->route('admin.maincategories')->with(['success'  => 'تم التفعيل بنجاح ']);

        }catch(\Exception $ex){

            return redirect()->route('admin.maincategories')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }

    }



}

