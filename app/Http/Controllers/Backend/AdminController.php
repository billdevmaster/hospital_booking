<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Locations;
use App\Models\LocationServices;
use App\Models\LocationPesuboxs;
use App\Models\Orders;
use App\Models\Services;
use App\Models\Bookings;
use Carbon\Carbon;

class AdminController extends Controller
{
    
    //
    public function index(Request $request) {
        $menu = "home";
        $locations = Locations::where("is_delete", 'N')->get();
        $current_location_id = $request->location_id ? $request->location_id : (count($locations) > 0 != null ? $locations[0]->id : 0);
        $date = $request->date ? $request->date: date("Y-m-d");
        $search_input = $request->search_input ? $request->search_input : "";
        if ($search_input != "") {
            $orderWithKeyword = Bookings::where("location_id", $current_location_id)->where(function($query1) use($request) {
                $query1->where("first_name", "like", '%' . $request->search_input . '%');
                $query1->orwhere("last_name", "like", '%' . $request->search_input . '%');
            })->where("is_delete", 'N')->first();
            if ($orderWithKeyword) {
                $date = $orderWithKeyword->date;
            }
        }
        return view('backend.home.index', compact("menu", "locations", "current_location_id", "search_input", "date"));
    }
    
    public function getCalendar(Request $request) {
        $location = Locations::find($request->current_location_id);
        $colors = [
            "green" => '#b1deb1',
            "yellow" => '#ffff94',
            "red" => '#fc7676',
            "blue" => '#a1caf1',
            "light-grey" => '#d3d3d3',
        ];
        $start_date = $request->start_date ? $request->start_date : date("Y-m-d");
        $year = date("M Y", strtotime($start_date));
        $timestamp = strtotime($start_date);
        $day = date('D', $timestamp);
        $location_date_start_time = $location[$day . "_start"];
        $pesuboxs = LocationPesuboxs::where("location_id", $request->current_location_id)->where("is_delete", 'N') ->orderByRaw("display_order ASC, id ASC")->get();
        if ($request->search_input && $request->search_input != "") {
            // $orderWithKeyword = Bookings::where("location_id", $request->current_location_id)->where(function($query1) use($request) {
                //     $query1->where("first_name", "like", '%' . $request->search_input . '%');
                //     $query1->orwhere("last_name", "like", '%' . $request->search_input . '%');
            // })->where("is_delete", 'N')->first();
            // if ($orderWithKeyword) {
            //     $start_date = $orderWithKeyword->date;
                $orders = Bookings::where("location_id", $request->current_location_id)->where(function($query1) use($request) {
                    $query1->where("first_name", "like", '%' . $request->search_input . '%');
                    $query1->orwhere("last_name", "like", '%' . $request->search_input . '%');
                })->where("date", $start_date)->where("is_delete", 'N')->get();
            // } else {
            //     $orders = [];
            // }
        } else {
            $orders = Bookings::select(["bookings.*", "location_pesuboxs.is_delete"])->leftJoin("location_pesuboxs", "location_pesuboxs.id", "=", "bookings.pesubox_id")->where("bookings.location_id", $request->current_location_id)->where("bookings.date", $start_date)->where("bookings.is_delete", 'N')->where("location_pesuboxs.is_delete", "N")->get();
        }
        
        $data = [];
        foreach($orders as $order) {
            $time_start = explode(':', $order->time);
            $location_start = explode(':', $location_date_start_time);
            if (($time_start[0] * 1 * 60 + $time_start[1]) >= ($location_start[0] * 1 * 60 + $location_start[1])) {
                // check pesubox is avaiable.
                $item = [];
                $item['uid'] = $order->id;
                $item['begins'] = $order->date . ' ' . $order->time;
                $endTime = strtotime("+" . $order->duration . " minutes", strtotime($item['begins']));
                $item['ends'] = $order->date . ' ' . date('H:i:s', $endTime);
                $item['color'] = $colors[$order->type];
                $item['resource'] = "@" . $order->pesubox_id;
                $item['title'] = substr($order->time, 0, 5) . " " . $order->first_name . " " . $order->last_name;
                $item['notes'] = "nimi: " . $order->first_name . " " . $order->last_name . "\n" . "sünnikuupäev: " . $order->birth_date . "\n" . "telefon: " . $order->phone . "\n" . "meili: " . $order->email . "\n" . "sõnum: " . $order->message;
                $item['notes'] .= "\n" . "teenuseid: ";
                $arr_service = explode(",", $order->service_id);
                foreach($arr_service as $service_id) {
                    if ($service_id != null) {
                        $service = Services::find($service_id);
                        $item['notes'] .= $service->name . ", ";
                    }
                }
                // $item['notes'] = "test";
                $data[] = (object)$item;
            }
        }
        // get start time and end time.
        $day = mktime(0, 0, 0, substr($start_date, 5, 2), substr($start_date, 8, 2), substr($start_date, 0, 4));
        $start_time = $location[date("D", $day) . '_start'];
        $end_time = $location[date("D", $day) . '_end'];
        return view('backend.home.components.calendar', compact("start_date", "pesuboxs", "data", "year", "location", "start_time", "end_time"))->render();
    }

    public function editOrder(Request $request) {
        $id = $request->id;
        $location_id = $request->location_id;
        $order = Bookings::find($id);

        $location_services = LocationServices::leftJoin('services', 'services.id', '=', 'location_services.service_id')->where("location_id", $request->location_id)->where("services.is_delete", "N")->get();
        $location_pesuboxs = LocationPesuboxs::where("location_id", $request->location_id)->where("is_delete", 'N')->get();
        $end_time = "";
        if ($order != null) {
            $time = Carbon::parse($order->date . " " . $order->time);
            $end_time = $time->addMinutes($order->duration)->format('Y-m-d H:i:s');
        }

        // $end_time = $time->format('Y-m-d H:i');
        $location = Locations::find($location_id);
        $interval = $location->interval;
        $location_lasttimes = json_encode([
            "1" => $location->Mon_end,
            "2" => $location->Tue_end,
            "3" => $location->Wed_end,
            "4" => $location->Thu_end,
            "5" => $location->Fri_end,
            "6" => $location->Sat_end,
            "0" => $location->Sun_end,
        ]);
        $order_services = [];
        if ($order) {
            $order_service_ids = explode(",", $order->service_id);
            if ($order_service_ids[0] != '') {
                foreach($order_service_ids as $service_id) {
                    $service = Services::find($service_id);
                    array_push($order_services, $service);
                }
            }
        }
        return view('backend.home.components.modal', compact("order", "id", "location_lasttimes", "location_id", "location_services", "location_pesuboxs", "end_time", "order_services", "interval"))->render();
    }

    public function updateOrder(Request $request) {
        $timestamp = strtotime(substr($request->datetime, 0, 10));
        $day = date('D', $timestamp);
        $location = Locations::find($request->location_id);
        $location_date_end_time = $location[$day . "_end"];

        $order = Bookings::find($request->id);
        if ($order == null) {
            $order = new Bookings();
            // check start time in database already.
            $order_already = Bookings::where("location_id", $request->location_id)->where('pesubox_id', $request->pesubox_id)->where("is_delete", "N")
                ->where(function($query1) use($request) {
                    $query1->where(function($query) use($request)
                    {
                        $query->where("started_at", "<=", $request->datetime);
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration - 1 MINUTE)"), ">", $request->datetime);
                    });
                    $query1->orwhere(function($query) use($request)
                    {
                        $query->where("started_at", "<", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . ($request->duration - 1) . ' minutes')));
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration - 1 MINUTE)"), ">", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . $request->duration . ' minutes')));
                    });
                    $query1->orwhere(function($query) use($request)
                    {
                        $query->where("started_at", ">", $request->datetime);
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration MINUTE)"), "<", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . $request->duration . ' minutes')));
                    });
                })
                ->first();
        } else {
            $order_already = Bookings::where("location_id", $request->location_id)->where('pesubox_id', $request->pesubox_id)->where("is_delete", "N")
                ->where(function($query1) use($request) {
                    $query1->where(function($query) use($request)
                    {
                        $query->where("started_at", "<=", $request->datetime);
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration - 1 MINUTE)"), ">", $request->datetime);
                    });
                    $query1->orwhere(function($query) use($request)
                    {
                        $query->where("started_at", "<", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . ($request->duration - 1) . ' minutes')));
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration - 1 MINUTE)"), ">", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . $request->duration . ' minutes')));
                    });
                    $query1->orwhere(function($query) use($request)
                    {
                        $query->where("started_at", ">", $request->datetime);
                        $query->where(DB::raw("DATE_ADD(started_at, INTERVAL duration MINUTE)"), "<", date("Y-m-d H:i:s", strtotime($request->datetime. ' + ' . $request->duration . ' minutes')));
                    });
                })
                ->where("id", "!=", $request->id)->first();
        }

        if ($order_already != null) {
            // check that order_already exceed the range of working time.
            $end_time = date('Y-m-d H:i:s', strtotime($order_already->started_at. ' +' . $order_already->duration . ' minutes')); 
            
            if ($end_time <= $order_already->date . " " . $location_date_end_time) 
                return response(json_encode(['success' => false, "message" => "Your booking time was already booked"]));
        } 
        $order->location_id = $request->location_id;
        if ($request->first_name != null) 
            $order->first_name = $request->first_name;
        else
            $order->first_name = '';

        if ($request->last_name != null) 
            $order->last_name = $request->last_name;
        else
            $order->last_name = '';

        if ($request->email != null) 
            $order->email = $request->email;
        else
            $order->email = '';

        if ($request->phone != null) 
            $order->phone = $request->phone;
        else
            $order->phone = '';

        if ($request->message != null) 
            $order->message = $request->message;
        else
            $order->message = '';
        
        $order->pesubox_id = $request->pesubox_id;
        $order->service_id = $request->service_id;
        $order->duration = $request->duration;
        $order->type = $request->type;
        $order->birth_date = $request->birth_year . "-" . str_pad($request->birth_month, 2, '0', STR_PAD_LEFT) . "-" . str_pad($request->birth_date, 2, '0', STR_PAD_LEFT);
        $order->started_at = $request->datetime;
        $order->date = substr($request->datetime, 0, 10);
        $order->time = substr($request->datetime, 11, 5) . ":00";
        $end_time = date('Y-m-d H:i:s', strtotime($order->started_at. ' +' . $order->duration . ' minutes'));
        if ($end_time > $order->date . " " . $location_date_end_time) 
            return response(json_encode(['success' => false, "message" => "Your booking time is over the day"]));
        $order->save();
        return response(json_encode(['success' => true]));
    }

    public function deleteOrder(Request $request) {
        $order = Bookings::find($request->id);
        $order->is_delete = 'Y';
        $order->save();
        return response(json_encode(['success' => true]));
    }
 
    public function getModel(Request $request) {
        $location_mark_models = MarkModel::where("mark_id", $request['mark_id'])->get();
        return view('backend.home.components.model', compact("location_mark_models"))->render();
    }

    public function getDayEndTime(Request $request) {
        $day = mktime(0, 0, 0, substr($request->date, 5, 2), substr($request->date, 8, 2), substr($request->date, 0, 4));
        $location = Locations::find($request->location_id);
        $bookings = Bookings::where("date", substr($request->date, 0, 10))->where("started_at", ">", $request->date)->where("pesubox_id", $request->pesubox_id)->where('is_delete', 'N')->orderBy('time', 'asc')->first();

        if ($bookings != null) {
            $time_end = $bookings['time'];
        } else {
            $time_end = $location[date("D", $day) . '_end'];
        }
        $from_time = strtotime($request->date);
        $to_time = strtotime(substr($request->date, 0, 10) . " " . $time_end);
        $difference = round(abs($to_time - $from_time) / 60,2);
        
        return response(json_encode(['difference' => $difference, "end_time" => substr($request->date, 0, 10) . " " . $time_end]));
    }
}