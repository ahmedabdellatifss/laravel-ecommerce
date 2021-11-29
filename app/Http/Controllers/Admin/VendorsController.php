<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\Vendor;
use App\Http\Requests\VendorRequest;

use Illuminate\Support\Facades\Notification;
use App\Notifications\VendorCreated;
use DB;
use Illuminate\Support\Str;

class VendorsController extends Controller
{
    public function index()
    {
        $vendors = Vendor::selection()->paginate(PAGINATION_COUNT);
        return view('admin.vendors.index', compact('vendors'));
    }


    public function create()
    {
        $categories = MainCategory::where('translation_of' , 0 )->active()->get();
        return view('admin.vendors.create' , compact('categories'));
    }


    public function store(VendorRequest $request)
    {
        try {
            if(!$request ->has('active'))
                $request ->request->add(['active'=>0]);
            else
                $request ->request->add(['active'=>1]);


           // save image
            $filePath = '';
           if($request->has('logo')) { //#17
                $filePath = uploadImage('vendors' , $request->logo);  //#32
            }

            Vendor::create([
                            'name' => $request->name,
                            'mobile' => $request->mobile,
                            'email' => $request->email,
                            'active' => $request->active,
                            'address' => $request->address,
                            'logo'    => $filePath,
                            'password' => $request->password,
                            'category_id' => $request->category_id,
                            'latitude' => $request->latitude,
                            'longitude' => $request->longitude,
                ]);
            //Notification::send($vendor, new VendorCreated($vendor));  //#32
                return redirect()->route('admin.vendors')->with(['success'  => 'تم الحفظ بنجاح']);

            }catch (\Exception $ex) {
                return $ex;
                return redirect()->route('admin.vendors')->with(['errors'  => 'هناك خطأ ما يرجا المحاوله مرة ثانيه']);
            }



    }


    public function edit($id)
    {
        try{
            $vendor = Vendor::Selection()->find($id);
            if(!$vendor)
                return redirect()->route('admin.vendors')->with(['errors'  => 'هذا المتجر غير موجود']);

            $categories = MainCategory::where('translation_of' , 0 )->active()->get();
            return view('admin.vendors.edit' , compact('vendor' , 'categories'));


        }catch(\Exception $exception ){
            return redirect()->route('admin.vendors')->with(['errors'  => 'هناك خطأ ما يرجا المحاوله مرة ثانيه']);
        }


    }
    public function update($id , VendorRequest $request)
    {
        try {

            $vendor = Vendor::Selection()->find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error' => 'هذا المتجر غير موجود او ربما يكون محذوفا ']);


            DB::beginTransaction();
            //photo
            if ($request->has('logo') ) {
                $filePath = uploadImage('vendors', $request->logo);
                Vendor::where('id', $id)
                    ->update([
                        'logo' => $filePath,
                    ]);
            }


            if (!$request->has('active'))
                $request->request->add(['active' => 0]);
            else
                $request->request->add(['active' => 1]);

            $data = $request->except('_token', 'id', 'logo', 'password');


            if ($request->has('password') && !is_null($request->  password)) {

                $data['password'] = $request->password;
            }

            Vendor::where('id', $id)
                ->update(
                    $data
                );

            DB::commit();
            return redirect()->route('admin.vendors')->with(['success' => 'تم التحديث بنجاح']);
        } catch (\Exception $exception) {
            return $exception;
            DB::rollback();
            return redirect()->route('admin.vendors')->with(['error' => 'حدث خطا ما برجاء المحاوله لاحقا']);
        }
    }


    public function destroy($id)
    {

        try{

            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error'  => ' هذا المتجر غير موجود']);


            // remove image from folder #37
            $image = Str::after($vendor->logo , 'assets/');
            $image = base_path('assets/'.$image);
            unlink($image); //delete from folder

            // delete image from database
            $vendor->delete();

            return redirect()->route('admin.vendors')->with(['success'  => 'تم الحذف المتجر بنجاح']);

        }catch(\Exception $ex){

            return redirect()->route('admin.vendors')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }
    }


    public function changeStatus($id) //#38
    {

        try{
            $vendor = Vendor::find($id);
            if (!$vendor)
                return redirect()->route('admin.vendors')->with(['error'  => ' هذا المتجر غير موجود']);

            $status = $vendor->active == 0 ? 1 : 0 ;

            $vendor->update(['active' =>$status]);

            return redirect()->route('admin.vendors')->with(['success'  => 'تم التفعيل بنجاح ']);

        }catch(\Exception $ex){

            return redirect()->route('admin.vendors')->with(['error'  => 'حدث خطأ ما برجاء المحاوله لاحقا  ']);

        }

    }


}
