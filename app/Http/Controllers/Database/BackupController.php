<?php

namespace App\Http\Controllers\Database;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;

class BackupController extends Controller
{
    public function index(){
        return view("database.index");
    }

    /**
     * This function creates a backup of the PostgreSQL database using pg_dump utility.
     *
     * @throws Exception if unable to backup database
     * @return JsonResponse with success message and command or errors message
     */
    public function backup(){
        try{
            set_time_limit(0);
            $configuration  = Configuration::first(["manual_backup"]);
            if($configuration->manual_backup == 0){
                return response()->json(["errors" => ["<strong>Manual backup is disabled!</strong> <br> You can enable it on Configuration page!"]],400);
            }
            $this->runBackup();
            return response()->json(["success" => true,"message"=>"Database has ben backup!"]);
        }catch(Exception $e){
            return response()->json(["errors" => ["Unable to backup DB : ".$e->getMessage()]],400);
        }
    }

    public function runBackup(){
        $filepath = sprintf('backup/database/backup_%s.sql', now()->format('Y_m_d'));
        $command = sprintf(
            'pg_dump --dbname=postgresql://%s:%s@localhost:5432/%s -c -f %s',
            config('database.connections.pgsql.username'),
            config('database.connections.pgsql.password'),
            config('database.connections.pgsql.database'),
            public_path($filepath)
        );
        system($command);
        // $process = Process::command($command);
        // $process->run();
        return $filepath;
    }

    public function download($file){
        try{
            $file = explode("|",base64_decode($file))[0];
            return response()->download(public_path("backup/database/$file"),"DB Backup eGateway.sql");
        }catch(Exception $e){
            return redirect()->back()->withErrors(["error" => "File not found!"]);
        }
    }
    public function destroy($file){
       try{
            $file = base64_decode($file);
            if(File::delete(public_path("backup/database/$file"))){
                return response()->json(['success'=>true,'message' => "File deleted!"]);
            }
            return response()->json(["errors" => ["Unable to delete file!"]]);
       }catch(Exception $e){
            return response()->json(["errors" => [$e->getMessage()]]);
       }
    }
    public function datatable(){
        $files = File::files(public_path("backup/database"));
        $listFiles = [];
        foreach ($files as $key=> $file) {
            $listFiles[$key]["file"] = Str::title($file->getFilename());
            $listFiles[$key]["size"] = round($file->getSize() / 1e+6,3);
            $listFiles[$key]["updated_at"] = \Carbon\Carbon::parse($file->getMTime())->diffForHumans();
            $listFiles[$key]["download"] = route("database.backup.download", base64_encode("{$file->getFileName()}|".Str::random(32)));
            $listFiles[$key]["hash"] = base64_encode($file->getFilename());
        }
        $collection = collect($listFiles);
        return DataTables::of($collection)->toJson();
    }

    public function restore(Request $request){

        try{
            set_time_limit(0);
            $request->validate([
                'file' => 'required|file|mimetypes:application/sql,application/postgresql,text/plain'
            ],[
                'file.required' => 'SQL File is required!',
                'file.mimetypes' => 'Wrong format SQL File!',
            ]);
            $newName = "restore_point_".date("Y_m_d_h").".sql";
            $path = $request->file("file")->storeAs("restore",$newName,"public");
            
            //psql --dbname=postgresql://postgres:root@localhost:5432/$dbName
            $command = sprintf(
                'psql --dbname=postgresql://%s:%s@localhost:5432/%s -q -f %s',
                config('database.connections.pgsql.username'),
                config('database.connections.pgsql.password'),
                config('database.connections.pgsql.database'),
                public_path("storage/$path")
            );
            system($command);
            // $process = Process::command($command);
            // $process->run();
            File::delete(public_path("storage/$path"));
            return redirect()->back()->withSuccess("Database has ben restore!");
        }catch(Exception $e){
            return redirect()->back()->withErrors([ $e->getMessage()]);
        }
    }
}
