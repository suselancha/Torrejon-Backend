<?php

namespace App\Models\Configuration;

use App\Models\Account\Account;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Bank extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    public function accounts() 
    {
        return $this->hasMany(Account::class);
    }

    public function createModel($request)
    {
        $name = strtoupper($request->name);

        $bank = new Bank();

        $bank->name = $name;

        try{        
            DB::beginTransaction();

            $result['success'] = $bank->save();
                
            DB::commit();            
                    
            $result['bank'] = $bank;
        } catch(\Throwable $th) {
            DB::rollBack();

            Log::info($th);

            $result['success'] = false;
        
            $result['th'] = $th;

            return $result;
        }
                
        return $result;
    }

    public function updateModel($request, $bank)
    {
        $name = strtoupper($request->name);
        
        try{            
            $bank->name = $name;

            DB::beginTransaction();    
            
            $result['success'] = $bank->save();

            DB::commit();

            $result['bank'] = $bank;

        } catch(\Throwable $th) {
            DB::rollBack();

            Log::info($th);

            $result['success'] = false;
        
            $result['th'] = $th;

            return $result;
        }
                
        return $result;
    }

    public function deleteModel($id)
    {   
        try{
            $bank = Bank::findOrFail($id);
        
            DB::beginTransaction();    
            
            $result['success'] =  $bank->delete();
        
            DB::commit();
        
            $result['bank'] = $bank;

        } catch(\Throwable $th) {
            DB::rollBack();

            Log::info($th);

            $result['success'] = false;

            $result['th'] = $th;

            return $result;
        }

        return $result;
    }
}
