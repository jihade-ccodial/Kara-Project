<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class TranslationsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $language_folder = base_path().'/lang';
        $files = File::glob("$language_folder/*.json");
        $translations=[];
        foreach ($files as $file) {
            //echo "$filename size " . filesize($filename) . "\n";
            $translations[] = File::name($file);
        }
        return view('admin.translations.index')->with([
            'translations' =>  $translations
        ]);
    }

    public function datatable(Request $request){
        $input = $request->all();
        $language = $input['language'];

        $language_folder = base_path().'/lang';
        $file = json_decode(file_get_contents($language_folder.'/'.$language.'.json'), true);
        $return = []; //$id=0;
        foreach ($file as $key=>$value)
            $return[] = array(
                //'DT_RowId' => 'row_'.$id,
                //'id' => $id++,
                'key'=>$key,
                'string'=>$value
            );

        die(json_encode($return));
    }

    public function store(Request $request)
    {
        $input = $request->all();
    }

    public function update(Request $request, $id)
    {
        $input = $request->all();
        $language = $input['language'];

        $language_folder = base_path().'/resources/lang';
        $file = json_decode(file_get_contents($language_folder.'/'.$language.'.json'), true);
        /*
        $c=0; $rkey='';
        foreach ($file as $key=>$value){
            if ($c==$id){
                $file[$key] = $input['data'][$id]['string'];
                $rkey=$key;
                break;
            }
            else $c++;
        }*/
        $file[$id] = $input['data'][$id]['string'];
        file_put_contents($language_folder.'/'.$language.'.json', json_encode($file));

       die(json_encode( array('data' => array(array(
                                //'DT_RowId' => 'row_'.$id,
                                //'id'=>$id,
                                'key'=>$id,
                                'string'=>$file[$id]
                            ))
                        )
       ));
    }

    public function destroy(Request $request, $id)
    {
        $input = $request->all();
        $language = $input['language'];

        $language_folder = base_path().'/resources/lang';
        $file = json_decode(file_get_contents($language_folder.'/'.$language.'.json'), true);

        unset($file[$id]);
        file_put_contents($language_folder.'/'.$language.'.json', json_encode($file));

        die(json_encode( array() ));
    }
}
