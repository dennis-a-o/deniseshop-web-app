<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use App\Models\MediaFile;
use App\Models\MediaFolder;
use ZipArchive;

/**
 * @since 05/27/2024 12:00 PM
 * @author Dennis Asuga
 **/
class MediaController extends Controller
{
    public function index()
    {
        return view('admin.media.index');
    }

    /**
     * Dispaly media on popup image select
     * */
    public function window()
    {
        return view('admin.media.window');
    }

    public function mediaList(Request $request)
    {
        $folderId = $request->folderId;
        $offset = $request->offset;
        $perPage = $request->perPage;
        $searchValue = $request->searchValue;
        $sortColumn = $request->sortColumn;
        $sorOrder = $request->sortOrder;
        $filter = $request->filter;

        $totalFiles = MediaFile::where("folder_id", $folderId)
            ->where("type", "like", "".$filter."%")
            ->where("name", "like", "%".$searchValue."%")->count();

        $mediaFiles = MediaFile::where("folder_id", $folderId)
            ->where("type", "like", "".$filter."%")
            ->where("name", "like", "%".$searchValue."%")
            ->orderBy($sortColumn, $sorOrder)
            ->skip($offset)
            ->take($perPage)
            ->get();

        $totalFolders = MediaFolder::where("parent_id", $folderId)
            ->where("name", "like", "%".$searchValue."%")->count();

        $folders = MediaFolder::where("parent_id", $folderId)
            ->where("name", "like", "%".$searchValue."%")
            ->when($sortColumn != "size", function($query)use($sortColumn,$sorOrder){
                $query->orderBy($sortColumn, $sorOrder);
            })
            ->skip($offset)
            ->take($perPage)
            ->get();

        $files = [];
        foreach($mediaFiles as $mediaFile){
            $size = "";

            $mediaSize = $mediaFile->size;
            if ($mediaSize > (1024*1024)) {
                $size = round($mediaSize/(1024*1024),2)." MB";
            }else{
                $size = round($mediaSize/(1024),2)." KB";
            }

            $files[] = [
                "id" => $mediaFile->id,
                "name" => $mediaFile->name,
                "url" => url('/storage').'/'.$mediaFile->url,
                "type" => $mediaFile->type,
                "size" =>  $size,
                "mime_type" => $mediaFile->mime_type,
                "created_at" => $mediaFile->created_at,
                "updated_at" => $mediaFile->updated_at,
            ]; 
        }

        $totalRecord = $totalFiles + $totalFolders;

        $breadCrumbs = $this->folderParent($folderId);

        $response = [
            "data" => [
                "files" => $files,
                "folders" => $folders,
                "breadcrumbs" => $breadCrumbs,
            ],
            "totalRecords" => $totalRecord,
        ];

        return response()->json($response, 200);

    }

    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folderId' => 'required|integer',
            'file.*' => 'required|file|mimes:jpg,jpeg,png,gif,txt,docx,zip,mp3,bmp,csv,xls,xlsx,ppt,pptx,pdf,mp4,doc,mpga,wav,webp,mov,ogg|max:51200',
        ],
        [
            'folderId.required' => 'Invalid form reload the page',
            'folderId.integer' => 'Invalid form reload the page'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "message" => $validator->errors()->all()]);
        }

        $path  = $this->folderPath($request->folderId);
      
    
        foreach($request->file as $file){
            $filenameWithExt = $file->getClientOriginalName();
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $name = $filename.'_'.time().'.'.$extension;
            $size = $file->getSize();
            // //move image
            $movePath = $file->move('storage/'.$path, $name);

            $mediaFile = new MediaFile();
            $mediaFile->user_id = Auth::user()->id;
            $mediaFile->name = $name;
            $mediaFile->folder_id = $request->folderId;
            $mediaFile->mime_type = $file->getClientMimeType();
            $mediaFile->size = $size;
            $mediaFile->url =  ($path == "")? $name : $path."/".$name;
            $mediaFile->type = $this->mediaType($extension);
            $mediaFile->created_at = now();
            $mediaFile->save();
        }
        
        return response()->json(["error" => false, "message" => "File uploaded succeffully."]);
    }

    public function createFolder(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'folderId' => 'required|integer',
            'folderName' => 'required|string|max:255',
        ],
        [
            'folderId.required' => 'Invalid form reload the page',
            'folderId.integer' => 'Invalid form reload the page'
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "message" => $validator->errors()->all()]);
        }

        $path = $this->folderPath($request->folderId);

        File::makeDirectory(public_path("/storage".'/'.$path.'/'.$request->folderName));

        $mFolder = new MediaFolder();
        $mFolder->user_id = Auth::user()->id;
        $mFolder->name = $request->folderName;
        $mFolder->slug = Str::slug($request->folderName);
        $mFolder->parent_id = $request->folderId;
        $mFolder->save();

        return response()->json(["error" => false, "message" => "Folder created succeffully."]);
    }

    public function download(Request $request)
    {
        $folderPath = $this->folderPath($request->folderId);
        $mediaFiles = $request->media;

        if (count($mediaFiles) == 1) {
            $key_id = key($mediaFiles);
            if ($mediaFiles[$key_id] == "folder") {
                $folder = MediaFolder::find($key_id);
                $zip = new ZipArchive;
                $filename = "download-".now()->format('Y-m-d-h-i-s').".zip";
                $filename = public_path('storage/'.$folderPath.'/'.$filename);
                if ($zip->open($filename, ZipArchive::CREATE) === TRUE){
                    $files = File::allFiles(public_path("storage/".$folderPath."/".$folder->name));
                    foreach ($files as $file) {

                        $relativeDir = File::dirname($file->getRelativePathname());
                        $relativePathname = "";

                        if ($relativeDir == ".") {
                            $relativePathname = $folder->name."/".File::basename($file); 
                        }else{
                            $relativePathname = $folder->name."/".$relativeDir.'/'.File::basename($file);
                        }

                        $zip->addFromString($relativePathname, $file->getPathname());
                    }
                    $zip->close();

                    if (File::exists($filename)) {
                        return response()
                            ->download($filename, File::name($filename).'.zip')
                            ->deleteFileAfterSend();
                    }
                } 
                return response()->json(["error" => true, "message" => "Media could not be downloaded."], 500);
            }else{
                $file = MediaFile::find(key($mediaFiles));
                return response()->download(public_path("storage/".$folderPath."/".$file->url));
            }
        }else{
            $zip = new ZipArchive;
            $filename = "download-".now()->format('Y-m-d-h-i-s').".zip";
            $filename = public_path('storage/'.$folderPath.'/'.$filename);
            if ($zip->open($filename, ZipArchive::CREATE) === TRUE) {
                foreach($mediaFiles as $id => $type){
                    if ($type == "folder") {
                        $folder = MediaFolder::find($id);
                        $files = File::allFiles(public_path("storage/".$folderPath."/".$folder->name));
                        foreach ($files as $file) {

                            $relativeDir = File::dirname($file->getRelativePathname());
                            $relativePathname = "";

                            if ($relativeDir == ".") {
                                $relativePathname = $folder->name."/".File::basename($file); 
                            }else{
                                $relativePathname = $folder->name."/".$relativeDir.'/'.File::basename($file);
                            }

                            $zip->addFromString($relativePathname, $file->getPathname());
                        }
                    }else{
                        $file = MediaFile::find($id);
                        $path = public_path('storage/'.$folderPath.'/'.$file->url);
                        $relative_name = basename($path);
                        $zip->addFile($path, $relative_name);
                    }
                }

                $zip->close();

                if (File::exists($filename)) {
                    return response()
                        ->download($filename, File::name($filename).'.zip')
                        ->deleteFileAfterSend();
                }
            }

            return response()->json(["error" => true, "message" => "Media could not be downloaded."], 500);
        }

        return response()->json(["error" => false, "message" => "Media items downloaded succeffully."],500);
    }

    public function makeCopy(Request $request)
    {
        $currentPath = $this->folderPath($request->folderId);

        foreach($request->media as $id => $type){
            if ($type == "folder") {
                $folder = MediaFolder::find($id);

                $fromFolder = public_path("storage/".$currentPath."/".$folder->name);
                $toFolder = public_path("storage/".$currentPath."/".$folder->name."-(copy)");

                $copyFolder = new MediaFolder;
                $copyFolder->user_id = $folder->user_id;
                $copyFolder->name = $folder->name."-(copy)";
                $copyFolder->slug = $folder->slug."-(copy)";
                $copyFolder->parent_id = $folder->parent_id;
                $copyFolder->created_at = now();
                $copyFolder->save();

                $this->copyFolder($id, $copyFolder->id);

                File::copyDirectory($fromFolder, $toFolder);
            }else{
                $file = MediaFile::find($id);

                $copyName = File::name($file->url);
                $copyExt = File::extension($file->url);

                $filePath = public_path("storage/".$currentPath."/".$file->url);
                $copyPath = public_path("storage/".$currentPath."/".$copyName."(copy)".".".$copyExt);

                $copy = new MediaFile;
                $copy->user_id = Auth::user()->id;
                $copy->name = $copyName."(copy)".".".$copyExt;
                $copy->mime_type = $file->mime_type;
                $copy->folder_id = $file->folder_id;
                $copy->size = $file->size;
                $copy->url = ($currentPath == "")? $copyName."(copy)".".".$copyExt : $currentPath."/".$copyName."(copy)".".".$copyExt;
                $copy->type = $file->type;
                $copy->options = $file->options;
                $copy->created_at = now();
                $copy->save();

                File::copy($filePath, $copyPath);
            }
        }

        return response()->json(["error" => false, "message" => "Media item copied succeffully.\n"]);
    }

    public function crop(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|exists:media_files,id",
            "image" => "required|image|max:4096"
        ]);

        if ($validator->fails()) {
            return response()->json(["error" => true, "message" => $validator->errors()->all()]);
        }

        $mediaFile = MediaFile::find($request->id);
        $mediaFile->size = $request->image->getSize();
        $mediaFile->save();

        $path  = $this->folderPath($request->folderId);

        $movePath = $request->image->move('storage/'.$path, $mediaFile->name);

        return response()->json(["error" => false, "message" => "Media item resized succeffully."]);
    }

    public function destroy(Request $request)
    {
        foreach($request->media as $id => $type){
            if ($type == "folder") {
                $this->deleteFolder($id);
            }else{
                $file = MediaFile::find($id);
                File::delete(public_path("storage"."/".$file->url));
                $file->delete();
            }
        }

        return response()->json(["error" => false, "message" => "Media items deleted succeffully.  "]);
    }

    /**
     * Copy folder and its contents(subfolders/files)
     * @param $id id of the folder
     * */
    private function copyFolder($oldId, $newCopyId)
    {
        $children = MediaFolder::where("parent_id", $oldId)->get();

        foreach($children as $child){
            $copyDir = new MediaFolder;
            $copyDir->user_id = $child->user_id;
            $copyDir->name = $child->name;
            $copyDir->slug = $child->slug;
            $copyDir->parent_id = $newCopyId;//new folder ids
            $copyDir->created_at = now();
            $copyDir->save();

            $this->copyFolder($child->id, $copyDir->id);
        }

        $files = MediaFile::where("folder_id", $oldId)->get();
        $path  = $this->folderPath($newCopyId);
        foreach($files as $file){
            $copy = new MediaFile;
            $copy->user_id = $file->user_id;
            $copy->name = $file->name;
            $copy->folder_id = $newCopyId;//new folder id
            $copy->mime_type = $file->mime_type;
            $copy->size = $file->size;
            $copy->url = ($path == "")? $file->name : $path."/".$file->name;
            $copy->type = $file->type;
            $copy->options = $file->options;
            $copy->created_at = now();
            $copy->save();
        }
    }

    /**
     * Deletes folder and its contents(subfolders/files)
     * @param $id id of the folder
     * */
    private function deleteFolder($id)
    {
       $children = MediaFolder::where("parent_id", $id)->get();
       foreach($children as $child){
            $this->deleteFolder($child->id);
       }

       $files = MediaFile::where("folder_id", $id)->get();
       foreach($files as $file){
            File::delete(public_path("storage"."/".$file->url));
       }

       MediaFile::where("folder_id", $id)->delete();

       $path = $this->folderPath($id);
       File::deleteDirectory(public_path("/storage/".$path));

       MediaFolder::where("id", $id)->delete();
    }

    /**
     * Gets full path of folder
     * @param $id of the folder
     * @return string full path folder
     * */
    private function folderPath($id)
    {
        $path = "";

        if ($id > 0) {
            $folder = MediaFolder::find($id);
            $path .= $folder->name;

            $parentId = $folder->parent_id;

            while(true){
                $parent = MediaFolder::where("id", $parentId)->first();
                if ($parent) {
                    $path = $parent->name."/".$path;
                    $parentId = $parent->parent_id;
                    continue;
                }
                break;
            }
        }

        return $path;
    }

      /**
     * Gets all folder parent name and id
     * @param $id of the folder
     * @return array of pair id and name or empty array  
     * */
    private function folderParent($id)
    {
        $parentFolder = [];

        if ($id > 0) {
            $folder = MediaFolder::find($id);

            $parentFolder[] = [
                "id" => $folder->id,
                "name" => $folder->name,
                "icon" => ""
            ];

            $parentId = $folder->parent_id;

            while(true){
                $parent = MediaFolder::where("id", $parentId)->first();

                if ($parent) {
                    $parentFolder[] = [
                        "id" => $parent->id,
                        "name" => $parent->name,
                        "icon" => ""
                    ];

                    $parentId = $parent->parent_id;
                    continue;
                }
                break;
            }
        }
        //reverse the array 
        if (count($parentFolder) > 1) {
           $parentFolder =  array_reverse($parentFolder);
        }
        //marge with root folder id=0
        $parentFolder = array_merge([["id" => 0, "name" => "All media", "icon" => '<i class="bi-files me-2"></i>']],$parentFolder);

        return $parentFolder;
    }

    /**
    * Check media type of file
    * @param  $extension of the media
    * @return string name of media
    * @NOTE not exhaustive
    */
    private function mediaType($extension)
    {
        $image = ["jpg","jpeg","png","gif"];
        $video = ["mp4","mpga","webp","mov"];

        if(in_array($extension, $image)){
            return "image";
        }elseif (in_array($extension, $video)) {
            return "video";
        }else{
            return "document";
        }
    }
}
