<?php

namespace App\Actions\Dataset;

use App\Http\Resources\SuccessfulOperationMessageResource;
use App\Models\Dataset;
use Illuminate\Support\Facades\Storage;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\HttpFoundation\Response;

class DeleteDataset
{
    use AsAction;

    public function handle(Dataset $dataset): SuccessfulOperationMessageResource
    {
        Storage::disk('datasets')->delete($dataset->path);

        $dataset->delete();

        return new SuccessfulOperationMessageResource([
            'message' => 'Dataset successfully deleted',
            'code' => Response::HTTP_OK,
        ]);
    }
}
