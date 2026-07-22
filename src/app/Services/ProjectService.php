<?php

namespace App\Services;

use App\Models\Project;
use App\Models\ForstwirtWorkingType;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ProjectService
{
    public function getDetailedProject(int $id): Collection
    {
        // Get the project details from the database
        $project = Project::findOrFail($id);
        $projectTitle = $this->getTitle($project);


        // Get the details for each role
        $rueckezug = $this->getRueckezugDetails($project);
        $harvester = $this->getHarvesterDetails($project);   
        $forstwirt = $this->getForstwirtDetails($project);
        
        $project = collect($project->toArray());
        $project->put('title', $projectTitle);
        
        // Create a collection to hold the project details and logs
        $projectDetailed = collect(array_filter([
            'project' => $project,
            'harvester' => $harvester,
            'rueckezug' => $rueckezug
        ]));

        foreach ($forstwirt as $roleDetails) {
            $projectDetailed->put($roleDetails['working_type'], collect([
                'sum' => $roleDetails['sum'],
                'logs' => collect($roleDetails['logs']),
            ]));
        }

        return $projectDetailed;
    }

    private function getHarvesterDetails(Project $project): ?Collection
    {
        $harvesterLogs = $project->harvesterLogs()->with('user')->get();

        if ($harvesterLogs->isEmpty()){
            return null;
        }

        $harvesterSum = $harvesterLogs->sum(function ($log) {
            return $this->timeToNumber($log->sum);
        });

        $harvesterLogs->map(function ($log){
            $log->date = Carbon::parse($log->date)->format('d.m.Y');
        });

        return collect([
            'logs' => $harvesterLogs,
            'sum' => $harvesterSum,
        ]);
    
    }

    private function getRueckezugDetails(Project $project): ?Collection
    {
        $rueckezugLogs = $project->rueckezugLogs()->with('user')->get();

        if ($rueckezugLogs->isEmpty()){
            return null;
        }

        $rueckezugSum = $rueckezugLogs->sum(function ($log) {
            return $this->timeToNumber($log->sum);
        });

        $rueckezugLogs->map(function ($log){
            $log->date = Carbon::parse($log->date)->format('d.m.Y');
        });

        return collect([
            'logs' => $rueckezugLogs,
            'sum' => $rueckezugSum,
        ]);
    }

    private function getForstwirtDetails(Project $project): Collection
    {

        //Get the logs for each role associated with the project
        $forstwirtLogs = $project->forstwirtLogs()->with('user')->get();

        $forstwirtLogs->map(function ($log){
            $log->date = Carbon::parse($log->date)->format('d.m.Y');
        });

        $forstwirtRolesSum = $forstwirtLogs->groupBy('working_type_id')->map(function ($logs, $roleId) {
            return [
                'working_type' => ForstwirtWorkingType::find($roleId)->slug,
                'sum' => $logs->sum(function ($log) {
                    return $this->timeToNumber($log->sum);
                }),
                'logs' => $logs,
            ];
        });

        return $forstwirtRolesSum;

    }

    private function timeToNumber($time)
    {
        $time = Carbon::parse($time);

        return $time->hour + ($time->minute / 60);
    }

    private function getTitle(Project $project): string
    {
        if ( $project->end_date){
            return $project->location . ' | ' . $project->date->format('m/Y') . ' - ' . $project->end_date->format('m/Y') . ' | ' . $project->client;
        }
        return $project->location . ' | ' . $project->date->format('m/Y') . ' | ' . $project->client;
    }

    public function getOpenProjects(int $workerId, string $from, string $to): Collection
    {
        $projects = Project::where('date', '<=', $to)
            ->where(function ($query) use ($from) {
                $query->where('end_date', '>=', $from)
                    ->orWhereNull('end_date');
            })
            ->where(function ($query) use ($workerId) {
                $query->whereHas('users', function ($q) use ($workerId) {
                    $q->where('user_id', $workerId);
                });
            })
            ->get();

        // Add project title to each project
        $projects->map(function ($project) {
            $project->title = $this->getTitle($project);
        });

        return $projects;
    }

    public function getProjectById(int $projectId): Project
    {
        // Get the project details from the database
        // findorFail will throw a ModelNotFoundException if the project does not exist
        $project =  Project::findOrFail($projectId);
        $project->title = $this->getTitle($project);
        return $project;
    }

    public function hasClosedProjects(int $workerId): bool
    {
        return Project::whereHas('users', function ($query) use ($workerId) {
            $query->where('user_id', $workerId);
        })->closedProjects()->exists();

    }
}