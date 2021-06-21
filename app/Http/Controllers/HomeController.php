<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application admin.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        date_default_timezone_set('America/Sao_Paulo');
        setlocale(LC_ALL, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');
        setlocale(LC_TIME, 'pt_BR.utf-8', 'ptb', 'pt_BR', 'portuguese-brazil', 'portuguese-brazilian', 'bra', 'brazil', 'br');

        $get_days = $get_mounth = $max_date = $min_date = $line_info = $bar_info = $pie_info = [];

        $get_billings = DB::table('billings')->get();

        foreach ($get_billings as $billings) {
            $line_chart_info = DB::table(DB::raw("(SELECT 1 AS weekday UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7) AS sig_ref"))->leftJoin('conversation_sessions AS cs', DB::raw('DAYOFWEEK(sig_cs.created_at)'), '=', 'ref.weekday')->where('cs.terminate', '=', '1')->where('cs.channel', '=', $billings->network)->groupBy('ref.weekday')->orderBy('ref.weekday')->get([
                DB::raw("sig_ref.weekday"),
                DB::raw("COALESCE(COUNT(sig_cs.id), 0) AS total"),
                DB::raw("sig_cs.channel"),
                DB::raw("MAX(sig_cs.created_at) AS max_date"),
                DB::raw("MIN(sig_cs.created_at) AS min_date"),
                DB::raw("CASE sig_ref.weekday WHEN 1 THEN 'Domingo' WHEN 2 THEN 'Segunda-Feira' WHEN 3 THEN 'Terça-Feira' WHEN 4 THEN 'Quarta-Feira' WHEN 5 THEN 'Quinta-Feira' WHEN 6 THEN 'Sexta-Feira' WHEN 7 THEN 'Sabado' ELSE ''END AS dayname"),
            ]);

            $bar_chart_info = DB::table(DB::raw("(SELECT 1 AS `month` UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12) AS sig_ref"))->leftJoin('conversation_sessions AS cs', DB::raw('MONTH(sig_cs.created_at)'), '=', 'ref.month')->where('cs.terminate', '=', '1')->where('cs.channel', '=', $billings->network)->groupBy('ref.month')->orderBy('ref.month')->get([
                DB::raw("sig_ref.month"),
                DB::raw("COALESCE(COUNT(sig_cs.id), 0) AS total"),
                DB::raw("sig_cs.channel"),
                DB::raw("CASE sig_ref.month WHEN 1 THEN 'Janeiro' WHEN 2 THEN 'Fevereiro' WHEN 3 THEN 'Março' WHEN 4 THEN 'Abril' WHEN 5 THEN 'Maio' WHEN 6 THEN 'Junho' WHEN 7 THEN 'Julho' WHEN 8 THEN 'Agosto' WHEN 9 THEN 'Setembro' WHEN 10 THEN 'Outubro' WHEN 11 THEN 'Novembro' WHEN 12 THEN 'Dezembro'  ELSE '' END AS monthname")
            ]);

            $stacked_chart_info = DB::table(DB::raw('sig_conversation_sessions cs,
            sig_conversation_sessions cs2'))->whereRaw('cs.terminate = 1')->whereRaw('cs.channel = "' . $billings->network . '"')->whereRaw('cs.channel = cs2.channel')->whereRaw('cs.created_at BETWEEN cs2.created_at AND cs2.updated_at')->groupBy(DB::raw('cs.created_at'))->orderBy(DB::raw('cs.created_at'))->get([DB::raw('cs.created_at'), DB::raw('cs.channel'), DB::raw('COUNT(*) AS CountSimultaneous')]);

            $line_info[ucwords($billings->network)] = [
                "Domingo" => 0,
                "Segunda-Feira" => 0,
                "Terça-Feira" => 0,
                "Quarta-Feira" => 0,
                "Quinta-Feira" => 0,
                "Sexta-Feira" => 0,
                "Sabado" => 0,
            ];

            foreach ($line_chart_info as $line_chart) {
                $line_info[ucwords($billings->network)][$line_chart->dayname] = $line_chart->total;
                $get_days[] = $line_chart->weekday . ' ' . $line_chart->dayname;
                $max_date[] = $line_chart->max_date;
                $min_date[] = $line_chart->min_date;
            }


            foreach ($bar_chart_info as $bar_chart) {
                $bar_info[ucwords($billings->network)][$bar_chart->monthname] = $bar_chart->total;
                $get_months[] = $bar_chart->month . ' ' . $bar_chart->monthname;
            }

            foreach ($stacked_chart_info as $stacked_chart) {
                $stacked_info[ucwords($billings->network)][Carbon::parse($stacked_chart->created_at)->formatLocalized('%B')][] = $stacked_chart->CountSimultaneous;
            }
        }
        foreach ($stacked_info as $channel => $stacked_month) {
            $stacked[ucwords($channel)] = [
                "Janeiro" => 0,
                "Fevereiro" => 0,
                "Março" => 0,
                "Abril" => 0,
                "Maio" => 0,
                "Junho" => 0,
                "Julho" => 0,
                "Agosto" => 0,
                "Setembro" => 0,
                "Outubro" => 0,
                "Novembro" => 0,
                "Dezembro" => 0,
            ];
            foreach ($stacked_month as $key => $value) {
                $stacked[$channel][ucwords($key)] = max($value);
            }
        }

        $pie_chart_info = DB::table('conversation_sessions')->whereNotNull('channel')->where('channel', '<>', 'chat')->groupBy('channel')->orderBy('channel')->get([
            DB::raw('COUNT(*) AS total'),
            DB::raw('channel')
        ]);

        $pie_chart_total = $pie_chart_info->sum('total');

        foreach ($pie_chart_info as $pie_chart) {
            /* if(strtolower($pie_chart->channel) == 'reclame_aqui'){
                dd(ucwords($pie_chart->channel,'_'));
            } */
            $percentage = number_format((($pie_chart->total / $pie_chart_total) * 100), 2);
            $pie_info[ucwords($pie_chart->channel, '_')] = $percentage;
        }
        $week_ini_date = Carbon::parse(min($min_date))->format('d/m/Y');
        $week_end_date = Carbon::parse(max($max_date))->format('d/m/Y');

        $border_colors = [
            'Facebook'      => '#4267B2',
            'Twitter'       => '#1DA1F2',
            'Whatsapp'      => '#25D366',
            'Reclame_Aqui'  => '#00874b'
        ];


        $line_chart_keys = array_keys($line_info);

        $bar_chart_keys = array_keys($bar_info);

        $pie_chart_keys = array_keys($pie_info);

        $stacked_chart_keys = array_keys($stacked_info);

        sort($line_chart_keys, SORT_STRING);

        sort($bar_chart_keys, SORT_STRING);

        sort($pie_chart_keys, SORT_STRING);

        sort($stacked_chart_keys, SORT_STRING);

        foreach ($line_chart_keys as $key => $line_network) {
            $line_datasets[] = [
                "label" => $line_network,
                "borderColor" => $border_colors[$line_network],
                'data' => $line_info[$line_network],
                'fill' => false,
                'borderWidth' => 3,
                'pointStyle' => 'rectRot',
                'pointRadius' => 5,
                'pointBorderColor' => $border_colors[$line_network],
                'backgroundColor' => $border_colors[$line_network],
            ];

            $line_max[] = max($line_info[$line_network]);
            $line_min[] = min($line_info[$line_network]);
            $hover_colors[] = $border_colors[$line_network];
        }

        foreach ($bar_chart_keys as $key => $bar_chart) {
            $bar_datasets[] = [
                "label" => $bar_chart,
                "borderColor" => $border_colors[$bar_chart],
                'data' => $bar_info[$bar_chart],
                'backgroundColor' => $border_colors[$bar_chart],
                'pointStyle' => 'rectRot',
                'pointRadius' => 5,
            ];

            $bar_max[] = max($bar_info[$line_network]);
            $bar_min[] = min($bar_info[$line_network]);
        }


        foreach ($pie_chart_keys as $pie_chart) {
            $pie_boder_colors[] = $border_colors[$pie_chart];
        }

        foreach ($stacked_chart_keys as $stacked_chart) {
            $stacked_datasets[] = [
                "label" => $stacked_chart,
                "borderColor" => $border_colors[$stacked_chart],
                'data' => $stacked[$stacked_chart],
                'backgroundColor' => $border_colors[$stacked_chart],
                'pointStyle' => 'rectRot',
                'pointRadius' => 5,
            ];

            $stacked_max[] = max($stacked[$stacked_chart]);
            $stacked_min[] = min($stacked[$stacked_chart]);
            $hover_stacked_colors[] = $border_colors[$stacked_chart];
        }

        $pie_datasets[] = [
            "label" => $pie_chart_keys,
            "borderColor" => $pie_boder_colors,
            'data' => array_values($pie_info),
            'backgroundColor' => $pie_boder_colors,
            'datalabels' => [
                'anchor' => 'center'
            ],
            'pointStyle' => 'rectRot',
            'pointRadius' => 5,
        ];

        $days = array_unique($get_days);

        $day = [];

        sort($days, SORT_STRING);

        foreach ($days as $value) {
            $day[] = trim(preg_replace('/[0-9]+/', '', $value));
        }

        $months = array_unique($get_months);
        sort($months, SORT_STRING);

        foreach ($months as $value) {
            $month[] = trim(preg_replace('/[0-9]+/', '', $value));
        }

        $stacked_month = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];

        $current_year = Carbon::now()->format('Y');

        $chart_titles = [
            "line"      => "Dados Semanais (" . $week_ini_date . "-" . $week_end_date . ")",
            "bar"       => "Dados Mensais (Ano: " . $current_year . ")",
            "pie"       => "Uso Total por midia (Ano: " . $current_year . ")",
            "stacked"   => "Pico de Acessos Simultâneos (Ano: " . $current_year . ")"
        ];

        $line_chartjs = app()->chartjs
            ->name('weekDayChart')
            ->type('line')
            ->size(['width' => 507.5, 'height' => 414.38])
            ->labels($day)
            ->datasets($line_datasets);

        $line_chartjs->optionsRaw('{
                "responsive": false,
                "scales": {
                    "x": {
                        grid: {
                            display: false,
                        },
                        "display": true,
                        "title": {
                            "display": true,
                            "text": "Dias"
                        }
                    },
                    "y": {
                        "suggestedMin": 0,
                        "suggestedMax": ' . max($line_max) . ',
                        "type": "linear",
                        "display": true,
                        "position": "left",
                        "title": {
                            "display": true,
                            "text": "Sessões"
                        },
                        "ticks": {
                            "stepSize": 10
                        }
                    },
                    "y1": {
                        "suggestedMin": 0,
                        "suggestedMax": ' . max($line_max) . ',
                        "type": "linear",
                        "display": true,
                        "position": "right",
                        "title": {
                            "display": true,
                            "text": "Sessões"
                        },
                        "ticks": {
                            "stepSize": 10
                        }
                    },
                },
                "interaction": {
                    "mode": "nearest",
                    "intersect": false,
                    "axis": "x"
                },
                transitions: {
                    show: {
                      animations: {
                        x: {
                          from: 0
                        },
                        y: {
                          from: 0
                        }
                      }
                    },
                    hide: {
                      animations: {
                        x: {
                          to: 0
                        },
                        y: {
                          to: 0
                        }
                      }
                    }
                },
                "stacked": false,
                "plugins": {
                    "legend": {
                        "labels": {
                            "usePointStyle": true
                        }
                    },
                    "tooltip": {
                        "usePointStyle": true,
                    }
                },
                "hoverRadius": 12,
                "hoverBackgroundColor": "[' . implode(",", $hover_colors) . ']",
                animation: {
                    onComplete: () => {
                      delayed = true;
                    },
                    delay: (context) => {
                      let delay = 0;
                      if (context.type === \'data\' && context.mode === \'default\' && !delayed) {
                        delay = context.dataIndex * 300 + context.datasetIndex * 100;
                      }
                      return delay;
                    },
                },
                animations: {
                    radius: {
                      duration: 400,
                      easing: "linear",
                      loop: (context) => context.active
                    }
                }
            }');

        $bar_chartjs = app()->chartjs
            ->name('monthChart')
            ->type('bar')
            ->size(['width' => 1000, 'height' => 500])
            ->labels($month)
            ->datasets($bar_datasets);

        $bar_chartjs->optionsRaw('{
                "responsive": false,
                "scales": {
                    "x": {
                        grid: {
                            display: false,
                        },
                        "display": true,
                        "title": {
                            "display": true,
                            "text": "Dias"
                        }
                    },
                    "y": {
                        "suggestedMin": 0,
                        "suggestedMax": ' . max($bar_max) . ',
                        "type": "linear",
                        "display": true,
                        "position": "left",
                        "title": {
                            "display": true,
                            "text": "Sessões"
                        },
                        "ticks": {
                            "stepSize": 10
                        }
                    },
                },
                "interaction": {
                    "mode": "nearest",
                    "intersect": false,
                    "axis": "x"
                },
                transitions: {
                    show: {
                      animations: {
                        x: {
                          from: 0
                        },
                        y: {
                          from: 0
                        }
                      }
                    },
                    hide: {
                      animations: {
                        x: {
                          to: 0
                        },
                        y: {
                          to: 0
                        }
                      }
                    }
                },
                "plugins": {
                    "legend": {
                        "labels": {
                            "usePointStyle": true
                        },
                        position: "top",
                    },
                    "tooltip": {
                        "usePointStyle": true,
                    }
                },
            }');

        $stacked_chartjs = app()->chartjs
            ->name('stackedChart')
            ->type('line')
            ->size(['width' => 1000, 'height' => 500])
            ->labels($stacked_month)
            ->datasets($stacked_datasets);

        $stacked_chartjs->optionsRaw('{
            "responsive": false,
            "scales": {
                "x": {
                    grid: {
                        display: false,
                    },
                    "display": true,
                    "title": {
                        "display": true,
                        "text": "Dias"
                    }
                },
                "y": {
                    "suggestedMin": 0,
                    "suggestedMax": ' . max($stacked_max) . ',
                    "type": "linear",
                    "display": true,
                    "position": "left",
                    "title": {
                        "display": true,
                        "text": "Sessões"
                    },
                    "ticks": {
                        "stepSize": 10
                    }
                },
                "y1": {
                    "suggestedMin": 0,
                    "suggestedMax": ' . max($stacked_max) . ',
                    "type": "linear",
                    "display": true,
                    "position": "right",
                    "title": {
                        "display": true,
                        "text": "Sessões"
                    },
                    "ticks": {
                        "stepSize": 10
                    }
                },
            },
            "interaction": {
                "mode": "nearest",
                "intersect": false,
                "axis": "x"
            },
            transitions: {
                show: {
                  animations: {
                    x: {
                      from: 0
                    },
                    y: {
                      from: 0
                    }
                  }
                },
                hide: {
                  animations: {
                    x: {
                      to: 0
                    },
                    y: {
                      to: 0
                    }
                  }
                }
            },
            "stacked": false,
            "plugins": {
                "legend": {
                    "labels": {
                        "usePointStyle": true
                    }
                },
                "tooltip": {
                    "usePointStyle": true,
                }
            },
            "hoverRadius": 12,
            "hoverBackgroundColor": "[' . implode(",", $hover_stacked_colors) . ']",
            animation: {
                onComplete: () => {
                  delayed = true;
                },
                delay: (context) => {
                  let delay = 0;
                  if (context.type === \'data\' && context.mode === \'default\' && !delayed) {
                    delay = context.dataIndex * 300 + context.datasetIndex * 100;
                  }
                  return delay;
                },
            },
            animations: {
                radius: {
                  duration: 400,
                  easing: "linear",
                  loop: (context) => context.active
                }
            }
        }');

        $pie_chartjs = app()->chartjs
            ->name('doughnutChart')
            ->type('doughnut')
            ->size(['width' => 507.5, 'height' => 414.38])
            ->labels($pie_chart_keys)
            ->datasets($pie_datasets);

        $pie_chartjs->optionsRaw('{
                "responsive": false,
                  plugins: {
                    tooltip: {
                        enabled: false
                    },
                    "legend": {
                        "labels": {
                            "usePointStyle": true
                        },
                        position: "top",
                    },
                    datalabels: {
                        backgroundColor: function(context) {
                          return context.dataset.backgroundColor;
                        },
                        borderColor: "white",
                        borderRadius: 25,
                        borderWidth: 2,
                        color: "#fff",
                        display: function(context) {
                          var dataset = context.dataset;
                          var count = dataset.data.length;
                          var value = dataset.data[context.dataIndex];
                          return value > count * 1.5;
                        },
                        font: {
                          weight: "bold"
                        },
                        padding: 6,
                        formatter: function(value, context) {
                            console.log(context);
                            return value + " %";
                          }
                      }
                  }
            },plugins: [ChartDataLabels],');

        return view('pages.admin.home.index')->with(compact('line_chartjs', 'bar_chartjs', 'pie_chartjs', 'stacked_chartjs',  'chart_titles'));
    }
}
