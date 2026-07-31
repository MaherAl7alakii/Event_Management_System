<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class ServiceLinkService
{

    public function link(Service $a, Service $b): void
    {
        if ($a->id === $b->id) {
            return;
        }

        [$lowId, $highId] = $this->orderedPair($a->id, $b->id);

        DB::table('service_links')->updateOrInsert(
            ['service_id' => $lowId, 'linked_service_id' => $highId],
            ['updated_at' => now(), 'created_at' => now()],
        );
    }


    public function unlink(Service $a, Service $b): void
    {
        [$lowId, $highId] = $this->orderedPair($a->id, $b->id);

        DB::table('service_links')
            ->where('service_id', $lowId)
            ->where('linked_service_id', $highId)
            ->delete();
    }

    public function linkedServiceIdsFor(int $serviceId): Collection
    {
        $asOrigin = DB::table('service_links')
            ->where('service_id', $serviceId)
            ->pluck('linked_service_id');

        $asTarget = DB::table('service_links')
            ->where('linked_service_id', $serviceId)
            ->pluck('service_id');

        return $asOrigin->merge($asTarget)->unique()->values();
    }


    private function orderedPair(int $idA, int $idB): array
    {
        return $idA < $idB ? [$idA, $idB] : [$idB, $idA];
    }
}
