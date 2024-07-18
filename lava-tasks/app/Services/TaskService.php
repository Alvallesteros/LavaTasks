<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskService
{
    //List Down Tasks in a Particular Project
    public function list(int $projectId)
    {
        return Task::with('project')->where('project_id', $projectId)->orderBy('priority')->get();
    }

    public function getById(int $id)
    {
        return Task::where('id', $id)->with('project')->first();
    }

    public function store($data): void
    {
        $count = Task::where('project_id', $data['project_id'])->count();
        $data['priority'] = $count + 1;

        Task::create($data);
    }

    public function update(int $id, array $data): void
    {
        $task = $this->getById($id);
        if(!$task) { return; }

        $task->update($data);
    }

    public function delete(int $id): void
    {
        $task = $this->getById($id);
        if (!$task) { return; }

        $task->delete();

        $tasks = Task::where('project_id', $task->project_id)->where('priority', '>', $task->priority)->get();
        if ($tasks->isEmpty()) { return; }

        $when_then = "";
        $where_in = "";

        foreach ($tasks as $task)
        {
            $when_then .= "WHEN ".$task->id."THEN ".($task->priority-1)." ";
            $where_in .= $task->id.",";
        }

        $table_name = (new Task())->getTable();
        $bulk_update_query = "UPDATE ".$table_name." SET priority = (CASE id ".$when_then."END)"." WHERE id IN(".substr($where_in, 0, -1).");";

        DB::update($bulk_update_query);
    }


    public function reorder(int $project_id, int $start, int $end): void
    {
        $items = Task::where('project_id', $project_id)->orderBy('priority')->pluck('priority', 'id')->toArray();

        if ($start > count($items) || $end > count($items)) { return; }

        $ids = [];
        $priorities = [];
        foreach ($items as $id => $priority)
        {
            $ids[] = $id;
            $priorities[] = $priority;
        }

        $out_priority = array_splice($priorities, $start - 1, 1);
        array_splice($priorities, $end - 1, 0 , $out_priority);

        $when_then = "";
        $where_in = "";

        foreach ($priorities as $out_k => $out_v)
        {
            $id = $ids[$out_v - 1];
            $when_then .= "WHEN ".$id." THEN ".($out_k + 1)." ";
            $where_in .= $id.",";
        }

        $table_name = (new Task())->getTable();
        $bulk_update_query = "UPDATE ".$table_name." SET priority = (CASE id ".$when_then."END) WHERE id IN(".substr($where_in, 0, -1).") AND deleted_at IS NULL;";

        DB::update($bulk_update_query);
    }

}
