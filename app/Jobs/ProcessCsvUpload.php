<?php 
namespace App\Jobs;

use App\Models\Product;
use App\Models\Upload;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class ProcessCsvUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $upload;

    public function __construct(Upload $upload)
        {
            $this->upload = $upload;
        }

        public function handle()
        {
            try{
                $this->upload->update(['status' => Upload::STATUS_PROCESSING]);

                $file = Storage::path($this->upload->path);

                if (!file_exists($file)) {
                    $this->upload->update([
                        'status' => Upload::STATUS_FAILED,
                        'error_message' => 'File not found'
                    ]);
                    return; 
                }

                $handle = fopen($file, 'r');

                if (!$handle) {
                    $this->upload->update([
                        'status' => Upload::STATUS_FAILED,
                        'error_message' => 'Cannot open file'
                    ]);
                    return;
                }

                $header = fgetcsv($handle, 0, ',');
                if (!$header) {
                    fclose($handle);
                    $this->upload->update([
                        'status' => Upload::STATUS_FAILED,
                        'error_message' => 'Invalid CSV header'
                    ]);
                    return;
                }

                $batchSize = 3000; // number of rows per batch
                $batch = [];
                $to_update_column = [
                    'UNIQUE_KEY',
                    'PRODUCT_TITLE',
                    'PRODUCT_DESCRIPTION',
                    'STYLE#',        
                    'SANMAR_MAINFRAME_COLOR',  
                    'SIZE',
                    'COLOR_NAME', 
                    'PIECE_PRICE',
                ];

                while (($data = fgetcsv($handle, 0, ',')) !== false) {

                    
                    $row = array_combine($header, $data);

                    $clean_row = [];

                    foreach ($row as $key => $value) {

                        $clean_key = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $key);
                        $clean_key = trim(mb_convert_encoding($clean_key, 'UTF-8', 'UTF-8'));

                        // clean the values
                        $clean_value = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value);
                        $clean_value = trim(mb_convert_encoding($clean_value, 'UTF-8', 'UTF-8'));

                        if(in_array($clean_key, $to_update_column)){
                            $clean_row[$clean_key] = $clean_value;
                        }

                    }
                    $batch[] = $clean_row;

                    // when batch is full, insert/update in transaction
                    if (count($batch) === $batchSize) {

                        Product::upsert(
                            $batch,
                            ['UNIQUE_KEY'],
                            $to_update_column
                        );
                        
                        $batch = [];
                    }
                }

                //insert remaining rows
                if (!empty($batch)) {
                    Product::upsert(
                        $batch,
                        ['UNIQUE_KEY'], 
                        $to_update_column
                    );
                }

                fclose($handle);

                $this->upload->update(['status' => Upload::STATUS_COMPLETED]);

                // delete file after processing
                Storage::delete($this->upload->path);

            }catch(\Throwable $e){

                $this->upload->update([
                    'status' => Upload::STATUS_FAILED,
                    'error_message' => $e->getMessage()
                ]);
                
                \Log::error('Job failed: ' . $e->getMessage());
            }

    }

    public function failed(\Throwable $e)
    {
        $this->upload->update([
            'status' => 'failed',
            'error_message' => $e->getMessage(),
        ]);
    }

}
