<?php
namespace App\Imports;

use App\Models\Schedule;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Concerns\WithEvents;




use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\OrganizationRole;
use App\User;
use App\Models\Teacher;
use App\Models\ClassModel;
use Illuminate\Support\Facades\DB;

class ScheduleImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    protected $version_id;
    protected $organization_id;
    protected $isInsert;
    protected $classId;

    public function __construct($version_id, $organization_id ,$isInsert)
    {
        $this->version_id = $version_id;
        $this->organization_id = $organization_id;
        $this->isInsert = $isInsert;
        //$this->classId = '1A1';
    }

    public function collection(Collection $rows)
    {
        //dd($this->classId);
        set_time_limit(300);
        $class = DB::table('classes as c')
            ->join('class_organization as oc','oc.class_id','c.id')
            ->where('c.nama',$this->classId)
            ->where ('oc.organization_id',$this->organization_id)
            ->select('c.id')->first();
        if($class == null){
            if($this->isInsert){
                $class = new ClassModel([
                    //
                    'nama'      => $this -> classId,
                    'levelid'   => 1,
                    'status'    => 1,
                ]);
        
                $class->save();
        
                DB::table('class_organization')->insert([
                    'organization_id' => $this->organization_id,
                    'class_id'        => $class->id,
                    'start_date'      => now(),
                ]);
            }
            else{
                throw ValidationException::withMessages(["error" => "Some classes is not in the records"]);
            }
        } 
        $existInVerison = DB::table('schedule_subject')
                            ->where('schedule_version_id',$this->version_id)
                            ->where('class_id',$class->id)->exists();
        if($existInVerison)
        {
             return;
        }else{
            DB::table('schedule_subject')->where('class_id',$class->id)->update(['status'=>0]);
        }

        foreach ($rows as $rowIndex =>$row) {

            $day = (int) $row->first();
            $i=-1;
            foreach ($row as $slot => $column) {
                $data =explode('-', $column);
                //dd($row,$slot,$column,$this->isInsert,$data);
                $i++;
                if(count($data) !=2)
                    continue;
                    //dd($data);
                $subjectCode =trim(strtoupper($data[0]));
                $subject=DB::table('subject')
                    ->where('organization_id',$this->organization_id)
                    ->where(function ($query) use ($subjectCode) {
                        $query->where('code', $subjectCode)
                              ->orWhere('name', $subjectCode);
                    })
                    ->first();
                  
                  
                if($subject ==null){
                    //dd("1");
                    if($this->isInsert){
                        $newId = DB::table('subject')->insertGetId([
                            'name'           =>  $subjectCode,
                            'code'          =>  $subjectCode,
                            'organization_id' => $this->organization_id
                        ]);
                        $subject = DB::table('subject')->where('id',$newId)->first();
                    }else{
                        
                        throw ValidationException::withMessages(["error" => "Some subject is not in the records"]);
                    }
                   
                }
                //dd($subject);
                $teacher = DB::table('users as u')
                    ->join('organization_user as ou','u.id','ou.user_id')
                    ->where('u.name',$data[1])
                    ->where('ou.role_id',5)
                    ->where('ou.organization_id',$this->organization_id)
                    ->where('ou.status',1)
                    ->select('u.*')
                    ->first();

                    
                if($teacher ==null){
                    if($this->isInsert){
                        $newteacher = DB::table('users')->insertGetId([
                            //
                            'name'      => $data[1],
                            // 'icno'      => $row['no_kp'],
                            'email'     => str_replace(' ','_',$data[1]). rand(100000, 999999) .'@prim.my',
                            'telno'     => '01000000000',
                            'password'  => Hash::make('abc123'),
                
                        ]);
                
                        //$newteacher->save();

                        DB::table('organization_user')->insert([
                            'organization_id' => $this->organization_id,
                            'user_id'       => $newteacher,
                            'role_id'       => 5,
                            'start_date'    => now(),
                            'status'        => 1,
                        ]);
                
                        $teacherRole = User::find($newteacher);
                        //dd($teacher,$newteacher);
                        // role pare,nt
                        $rolename = OrganizationRole::find(5);
                        $teacherRole->assignRole($rolename->nama);
                        $teacher = DB::table('users')->where('id',$newteacher)->first();
                    }else{
                        throw ValidationException::withMessages(["error" => "Some teachers is not in the records"]);
                    }
                    
                }
                //dd($teacher->id);
                $teacher = DB::table('users as u')
                ->join('organization_user as ou','u.id','ou.user_id')
                ->where('u.name',$data[1])
                ->where('ou.role_id',5)
                ->where('ou.organization_id',$this->organization_id)
                ->where('ou.status',1)
                ->select('u.*')
                ->first();
      
                //dd($subject,$class,$teacher);
                DB::table('schedule_subject')->insert([
                    'created_at'=>Carbon::now(),
                    'updated_at'=>Carbon::now(),
                    'schedule_version_id'=>$this->version_id,
                    'subject_id'=> $subject->id,
                    'class_id'=>$class->id,
                    'day'=>$day,
                    'slot'=>$i,
                    'teacher_in_charge'=>$teacher->id
                ]);
            }
           
        }
    }

    public function chunkSize(): int
    {
        return 250; // Adjust the chunk size as needed
    }

    public function registerEvents(): array
    {
        return [
            BeforeSheet::class => function (BeforeSheet $event) {
                $this->classId = $event->getSheet()->getDelegate()->getTitle();
                //dd('Sheet Name:', $this->classId);
            },
        ];
    }

    protected function getClassIdFromSheetName()
    {
        $sheetName = $this->classId;

        preg_match('/\d+/', $sheetName, $matches);
        //dd($sheetName);
        if (!empty($matches)) {
            return $matches[0];
        }
        
        return 'default_value_or_throw_exception';
    }
}
?>