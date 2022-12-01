<?php

    namespace Modules\Resilience\Exports\Sheets;

    use App\Workshop;
    use Maatwebsite\Excel\Concerns\WithEvents;
    use Maatwebsite\Excel\Events\AfterSheet;
    use Maatwebsite\Excel\Events\BeforeSheet;
    use Modules\Resilience\Entities\ConsultationAnswer;
    use Maatwebsite\Excel\Concerns\FromQuery;
    use Maatwebsite\Excel\Concerns\WithTitle;
    use Maatwebsite\Excel\Concerns\WithHeadings;
    use Maatwebsite\Excel\Concerns\FromCollection;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;
    use PhpOffice\PhpSpreadsheet\Cell\Cell;
    use PhpOffice\PhpSpreadsheet\Cell\DataType;
    use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
    use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

    class ExcelPerQuestionSheet extends DefaultValueBinder implements FromCollection, WithTitle, WithHeadings, ShouldAutoSize, WithEvents,WithCustomValueBinder
    {

        private $question;
        /**
         * @var string
         */
        private $cellRange;
        private $wrapCellRange;
        private $headingCellRange;

        public function bindValue(Cell $cell, $value)
        {
            if (is_numeric($value)) {
                $cell->setValueExplicit($value, DataType::TYPE_STRING);

                return TRUE;
            }

            // else return default behavior
            return parent::bindValue($cell, $value);
        }

        public function __construct($question)
        {
            $this->question = $question;
            $headingCellRange = 'A2:G2';
            $cellRange = 'A1:C1';
            $wrapCellRange = 'D2:D';
            $this->headingCellRange = $headingCellRange;
            if (in_array($this->question->consultation_question_type_id, [15])) {
                $cellRange = 'A1:B1';
                $wrapCellRange = 'B2:B';
            }
            if (in_array($this->question->consultation_question_type_id, [16])) {
                $cellRange = 'A1:D1';
                $wrapCellRange = 'D2:D';
            }
            if (in_array($this->question->consultation_question_type_id, [1, 6, 7, 12])) {
                $cellRange = 'A1:C1';
                $wrapCellRange = 'B2:B';
            }
            if (in_array($this->question->consultation_question_type_id, [2, 5, 9, 10, 11])) {
                $cellRange = 'A1:E1';
                $wrapCellRange = 'E2:E';
            }
            if (in_array($this->question->consultation_question_type_id, [3, 4])) {
                $cellRange = 'A1:C1';
                $wrapCellRange = 'C2:C';
            }
            if (in_array($this->question->consultation_question_type_id, [13, 14])) {
                $cellRange = 'A1:G1';
                $wrapCellRange = 'D2:D';
            }
            $this->cellRange = $cellRange;
            $this->wrapCellRange = $wrapCellRange;

        }


        /**
         * @return array
         */
        public function registerEvents(): array
        {
            $styleArray = [
                'font' => [
                    'bold' => TRUE,
                ],
            ];
            $wrapCellCount = ConsultationAnswer::select('user_id', 'answer', 'manual_answer', 'user_workshop_id', 'consultation_question_id')->with('user')->with('consultationQuestion')->where('consultation_question_id', $this->question->id)->count();

            $wrapCellCount = $wrapCellCount + 5;
            return [
                // Handle by a closure.
                BeforeSheet::class => function (BeforeSheet $event) use ($styleArray) {
                    $event->sheet->setCellValue('A1', $this->question->question)->mergeCells($this->cellRange);
                },
                AfterSheet::class  => function (AfterSheet $event) use ($styleArray, $wrapCellCount) {
                    $event->sheet->getStyle($this->headingCellRange)->applyFromArray($styleArray);
                    $event->sheet->getStyle($this->cellRange)->applyFromArray($styleArray);
                    $event->sheet->getStyle($this->wrapCellRange . $wrapCellCount)->getAlignment()->setWrapText(TRUE);
                },
            ];
        }

        /**
         * @return Builder
         */

        public function collection()
        {
            $answers = ConsultationAnswer::select('user_id', 'answer', 'manual_answer', 'user_workshop_id', 'consultation_question_id')->with('user')->with('consultationQuestion')->where('consultation_question_id', $this->question->id)->whereHas('user')->get();
            if (in_array($this->question->consultation_question_type_id, [15])) {
                $options = [];
                foreach ($answers as $key => $answer) {
                    $columnAnswers = collect(json_decode($answer->answer));
                    $qOptions = collect(json_decode($this->question->options));
                    foreach ($columnAnswers as $k => $value) {
                        if ($k == 0) {
                            $workshop = Workshop::find($answer->user_workshop_id);
                            $name = $workshop->workshop_name . " " . $answer->user->fname . " " . $answer->user->lname;
                            $option = collect($qOptions)->firstWhere('id', $value);
                            if (empty($option)) {

                                if (isset($answer->manual_answer) && !empty($answer->manual_answer)) {
                                    $manualAnswerData = collect(json_decode($answer->manual_answer));
                                    collect($manualAnswerData)->map(function ($v, $k) use (&$options, &$manualAnswerData, $value, $name) {
                                        if ($value == $v->id) {
                                            $label = $v->label;
                                            $options[] = [
                                                $label, $name,
                                            ];
                                        }

                                    });

                                }
                            } else {
                                $label = $option->label;
                                $options[] = [
                                    $label, $name,
                                ];
                            }
                        } else {
                            $option = collect($qOptions)->firstWhere('id', $value);
                            if (empty($option)) {
                                if (isset($answer->manual_answer) && !empty($answer->manual_answer)) {
                                    $manualAnswerData = collect(json_decode($answer->manual_answer));
                                    collect($manualAnswerData)->map(function ($v, $k) use (&$options, &$manualAnswerData, $value) {
                                        if ($value == $v->id) {
                                            $label = $v->label;
                                            $options[] = [
                                                $label, "",
                                            ];
                                        }
                                    });
                                }
                            } else {
                                $label = $option->label;
                                $options[] = [
                                    $label, "",
                                ];
                            }
                        }
                    }
                    $options[] = [
                        "", "",
                    ];
                }
                $answers = collect($options);
//dd($answers);
            }
            if (in_array($this->question->consultation_question_type_id, [16])) {
                $answers->each(function ($answer, $key) {
                    $options = collect(json_decode($this->question->options));
                    $columnAnswers = collect(json_decode($answer->answer));

                    $columnAnswers->each(function ($value, $k) use ($options, $answer) {
                        $option = collect($options['row'])->firstWhere('id', $k);
                        $label = $option->label;
                        $rowAnswer = collect($value)->values();
                        $rowAnswer->prepend($label);
                        if ($k == "row0") {
                            $workshop = Workshop::find($answer->user_workshop_id);
                            if (!empty($answer->user)) {
                                $name = $workshop->workshop_name . " " . $answer->user->fname . " " . $answer->user->lname;
                                $rowAnswer->push($name);
                            }
                        }
                        $value->rowAns = $rowAnswer;
                    });
                    $answer->userAns = $columnAnswers->pluck('rowAns');
                });
                $answers = $answers->pluck('userAns');

                $withEmptyRow = [];
                foreach ($answers as $key => $value) {
                    $withEmptyRow[] = $value;
                    $withEmptyRow[] = [""];
                }
                $answers = collect($withEmptyRow);
            }
            if (in_array($this->question->consultation_question_type_id, [13, 14])) {
                $pluckedAnswers = $answers->pluck('answer');
                $pluckedAnswerIds = $pluckedAnswers->groupBy(function ($item) {
                    return array_column(json_decode($item), 'id');
                })->keys()->toArray();
                $questionOptions = collect(json_decode($this->question->options));

                $unAnsweredOptions = $questionOptions->filter(function ($value, $k) use ($pluckedAnswerIds) {
                    if (!in_array($value->id, $pluckedAnswerIds)) {
                        return $value;
                    }
                });
                $ansCount = $answers->count();
                /*  $sum = 0;
                  foreach ($pluckedAnswers as $answerIndex => $answer) {

                      $sum += array_sum(array_column(json_decode($answer), 'scale_value'));
                  }

                  if ($ansCount > 0)
                      $sliderAvg = round($sum / $ansCount, 2);
                  else
                      $sliderAvg = 0;*/

                $answers = $answers->filter(function ($value, $k) {
                    if (!empty($value->answer)) {
                        return $value;
                    }
                });
                $groupedAnswer = $answers->groupBy(function ($item) {
                    return array_column(json_decode($item->answer), 'id');
                });
                $ans = [];
                foreach ($groupedAnswer as $key => $values) {
                    $arr = $selectedManualOptions = [];
                    $optionsData = collect(json_decode($this->question->options));

                    $selectedOption = $optionsData->filter(function ($value, $k) use ($key) {
                        if ($value->id === $key) {
                            return $value;
                        }
                    });

                    if ($selectedOption->isEmpty()) {

                        $values->map(function ($v, $k) use ($key, &$selectedManualOptions) {
                            $manualAnswerData = collect(json_decode($v->manual_answer));
                            $selectedManualOptions = $manualAnswerData->filter(function ($option) use ($key) {
                                return $option->id == $key;
                            })->values();
                        });
                        array_push($arr, $selectedManualOptions->implode('label', ', '));
                    } else {
                        array_push($arr, $selectedOption->implode('label', ', '));
                    }

                    array_push($arr, count($values));
                    array_push($arr, $ansCount);
                    $values->each(function ($item, $keyV) {
                        $workshop = Workshop::find($item->user_workshop_id);
                        $item->name = $workshop->workshop_name . " " . $item->user->fname . " " . $item->user->lname;
                    });

                    array_push($arr, $values->implode('name', PHP_EOL));
                    array_push($arr, number_format((count($values) / $ansCount) * 100, 2));

                    $values->each(function ($item, $itemK) use ($key) {
                        $itemAnswers = collect(json_decode($item->answer));
                        $item->choices = $itemAnswers->filter(function ($itemAnsValue, $itemAnsIndex) use ($key) {

                            if ($itemAnsValue->id == $key) {
                                if ($itemAnsValue->scale_value == 0) {
                                    return TRUE;
                                }
                                return $itemAnsValue->scale_value;
                            }
                        });
                    });
                    $sum = $values->pluck('choices')->flatten()->sum('scale_value');

                    if (count($values) > 0) {
                        $sliderAvg = (round($sum / count($values), 2));
                        if (empty($sliderAvg)) {
                            $sliderAvg = number_format($sliderAvg);
                        }
                    } else
                        $sliderAvg = number_format(0);

                    $choices = $values->pluck('choices')->flatten()->implode('scale_value', ', ');
                    if (empty($choices)) {
                        $choices = number_format(0);
                    }
                    array_push($arr, $choices);
                    array_push($arr, $sliderAvg);
                    array_push($ans, $arr);
                }
                $unAns = collect([]);
                if ($unAnsweredOptions->count() > 0) {
                    $unAns = $unAnsweredOptions->map(function ($optValue, $optIndex) {
                        return [$optValue->label, '', '', '', '', '', ''];
                    });
                }
                $allAnswers = array_merge($ans, $unAns->toArray());
                $answers = collect($allAnswers);
//                    dd($answers, $this->question);
            }
            if (in_array($this->question->consultation_question_type_id, [1, 6, 7, 12])) {
                $answers->each(function ($item, $key) {
                    $workshop = Workshop::find($item->user_workshop_id);
                    $item->name = $workshop->workshop_name . " " . $item->user->fname . " " . $item->user->lname;
                    $item->workshop = $workshop->workshop_name;
                    unset($item->user_workshop_id);
                    unset($item->user_id);
                    unset($item->manual_answer);
                    unset($item->consultation_question_id);
                });
            }
            if (in_array($this->question->consultation_question_type_id, [9, 10, 11])) {
                $ansCount = $answers->count();
                $groupedAnswer = $answers->groupBy('answer');
                $ans = [];
                foreach ($groupedAnswer as $key => $values) {
                    $arr = [];
                    array_push($arr, $key);
                    array_push($arr, count($values));
                    array_push($arr, $ansCount);
                    array_push($arr, number_format((count($values) / $ansCount) * 100, 2));
                    $values->each(function ($item, $key) {
                        $workshop = Workshop::find($item->user_workshop_id);
                        $item->name = $workshop->workshop_name . " " . $item->user->fname . " " . $item->user->lname;
                    });
                    array_push($arr, $values->implode('name', PHP_EOL));
                    array_push($ans, $arr);
                }
                $answers = collect($ans);
            }
            if (in_array($this->question->consultation_question_type_id, [2, 5])) {
                $ansCount = $answers->count();
                $groupedAnswer = $answers->groupBy('answer');
                $ans = [];
                foreach ($groupedAnswer as $key => $values) {
                    $arr = [];
                    if (in_array($this->question->consultation_question_type_id, [2])) {
                        if ($key === "yes") {
                            array_push($arr, "YES");
                        } else {
                            array_push($arr, "NO");
                        }
                    } else {
                        if ($key === "up") {
                            array_push($arr, "UP");
                        } elseif ($key === "down") {
                            array_push($arr, "DOWN");
                        } else {
                            array_push($arr, "STABLE");
                        }
                    }
                    array_push($arr, count($values));
                    array_push($arr, $ansCount);
                    array_push($arr, number_format((count($values) / $ansCount) * 100, 2));
                    $values->each(function ($item, $key) {
                        $workshop = Workshop::find($item->user_workshop_id);
                        $item->name = $workshop->workshop_name . " " . $item->user->fname . " " . $item->user->lname;
                    });
                    array_push($arr, $values->implode('name', PHP_EOL));
                    array_push($ans, $arr);
                }
                if (count($ans) > 0) {
                    $firstElements = array_map(function ($i) {
                        return $i[0];
                    }, $ans);
                    if (in_array($this->question->consultation_question_type_id, [2])) {
                        $unAnsOptions = array_diff(["YES", "NO"], $firstElements);
                    } else {
                        $unAnsOptions = array_diff(["UP", "DOWN", "STABLE"], $firstElements);
                    }
                    foreach ($unAnsOptions as $unAnsOption) {
                        array_push($ans, [$unAnsOption, "", "", "", ""]);
                    }
                }
                $answers = collect($ans);
            }
            if (in_array($this->question->consultation_question_type_id, [3])) {
                $pluckedAnswers = $answers->pluck('answer');
                $pluckedAnswers->transform(function ($item) {
                    return json_decode($item);
                });
                $pluckedAnswerIds = $pluckedAnswers->flatten()->unique()->toArray();
                $questionOptions = collect(json_decode($this->question->options));

                $unAnsweredOptions = $questionOptions->filter(function ($value, $k) use ($pluckedAnswerIds) {
                    if (!in_array($value->id, $pluckedAnswerIds)) {
                        return $value;
                    }
                });
                $groupedAnswer = $answers->groupBy('answer')->filter(function ($value, $k) {
                    if (!empty($k)) {
                        return $value;
                    }
                });
                $groupedAnswerManual = $answers->groupBy('manual_answer')->filter(function ($value, $k) {
                    if (!empty($k)) {
                        return $value;
                    }
                });

                $allAns = [];

                foreach ($groupedAnswer as $key => $item) {
                    $arr = [];
                    $optionsData = json_decode($key);
                    if ($optionsData) {
                        $selectedOptions = $questionOptions->filter(function ($option) use ($optionsData) {
                            return in_array($option->id, $optionsData);
                        })->values();
                        if (empty($selectedOptions->implode('label', ', '))) {
                            $item->map(function ($v, $k) use (&$arr) {
                                $manualAnswerData = collect(json_decode($v->manual_answer));
                                $arr[] = $manualAnswerData->implode('label', ', ');
                            });
                        } else {
                            $arr[] = $selectedOptions->implode('label', ', ');
                        }

                    } else {
                        $arr[] = '';
                    }
                    if (!$optionsData && isset($item->manual_answer)) {
                        $manualAnswerData = collect(json_decode($item->manual_answer));
                        $arr[] = $manualAnswerData->implode('label', ', ');
                    } else {
                        $arr[] = '';
                    }

                    $item->each(function ($itemVal, $itemIndex) {
                        $workshop = Workshop::find($itemVal->user_workshop_id);
                        $itemVal->name = $workshop->workshop_name . " " . $itemVal->user->fname . " " . $itemVal->user->lname;
                    });
                    $arr[] = $item->implode('name', PHP_EOL);
                    array_push($allAns, $arr);
                }

                /* foreach ($groupedAnswerManual as $key => $item) {
                     $arr = [];
                     $optionsData = json_decode($key, TRUE);

                     if (!empty($optionsData)) {
                         $optionsData = array_column($optionsData, 'id');
                     }
                     if ($optionsData) {
                         $item->map(function ($v, $k) use (&$arr) {
                             $manualAnswerData = collect(json_decode($v->manual_answer));
                             $arr[] = $manualAnswerData->implode('label', ', ');
                         });

                     } else {
                         $arr[] = '';
                     }
                     $arr[] = '';
                     $item->each(function ($itemVal, $itemIndex) {
                         $workshop = Workshop::find($itemVal->user_workshop_id);
                         $itemVal->name = $workshop->workshop_name . " " . $itemVal->user->fname . " " . $itemVal->user->lname;
                     });
                     $arr[] = $item->implode('name', PHP_EOL);
                     array_push($allAns, $arr);
                 }*/
                $ans = [];

                if ($unAnsweredOptions->count() > 0) {
                    foreach ($unAnsweredOptions as $key => $values) {
                        array_push($ans, ["answer" => $values->label, "manual_answer" => "", "name" => ""]);
                    }
                }
                $answers = collect(array_merge($allAns, $ans));

            }
            if (in_array($this->question->consultation_question_type_id, [4])) {
                $pluckedAnswers = $answers->pluck('answer');
                $pluckedAnswers->transform(function ($item) {
                    return json_decode($item);
                });
                $pluckedAnswerIds = $pluckedAnswers->flatten()->unique()->toArray();
                $questionOptions = collect(json_decode($this->question->options));
                $unAnsweredOptions = $questionOptions->filter(function ($value, $k) use ($pluckedAnswerIds) {
                    if (!in_array($value->id, $pluckedAnswerIds)) {
                        return $value;
                    }
                });
                $groupedAnswer = $answers->groupBy('answer');
//dd($groupedAnswer);
                $allAns = [];
                foreach ($groupedAnswer as $key => $item) {
                    $arr = [];
                    $selectedOptions = $selectedManualOptions = [];
                    $optionsData = json_decode($key);
                    if ($optionsData) {
                        collect($optionsData)->each(function ($itemVal, $itemIndex) use ($questionOptions, $optionsData, &$selectedOptions, $item, &$selectedManualOptions) {
                            if ($questionOptions->contains('id', $itemVal)) {
                                $selectedOptions = $questionOptions->filter(function ($option) use ($optionsData) {
                                    return in_array($option->id, $optionsData);
                                })->values();
                            } else {
                                $item->map(function ($v, $k) use (&$selectedManualOptions, $itemVal) {
                                    $manualAnswerData = collect(json_decode($v->manual_answer));
                                    $selectedManualOptions[] = $manualAnswerData->filter(function ($option) use ($itemVal) {
                                        return $option->id == $itemVal;
                                    })->values();
                                });
                            }
                        });

                        $arr[] = collect($selectedOptions)->merge(collect($selectedManualOptions)->flatten())->implode('label', ', ');
                    } else {
                        $arr[] = '';
                    }
                    $arr[] = '';
                    /*  $item->map(function ($v, $k) use (&$arr) {
                          if(!empty($v->manual_answer)){
                              $manualAnswerData = collect(json_decode($v->manual_answer));
                              $arr[] = $manualAnswerData->implode('label', ', ');
                          }else{
                              $arr[] = '';
                          }

                      });*/

                    $item->each(function ($itemVal, $itemIndex) {
                        $workshop = Workshop::find($itemVal->user_workshop_id);
                        $itemVal->name = $workshop->workshop_name . " " . $itemVal->user->fname . " " . $itemVal->user->lname;
                    });
                    $arr[] = $item->implode('name', PHP_EOL);
                    array_push($allAns, $arr);
                }

                $ans = [];

                if ($unAnsweredOptions->count() > 0) {
                    foreach ($unAnsweredOptions as $key => $values) {
                        array_push($ans, ["answer" => $values->label, "manual_answer" => "", "name" => ""]);
                    }
                }
                $answers = collect(array_merge($allAns, $ans));

            }
            if (in_array($this->question->consultation_question_type_id, [18])) {
                $pluckedAnswers = $answers->pluck('answer');
                $pluckedAnswerIds = $pluckedAnswers->groupBy(function ($item) {
                    return array_column(json_decode($item), 'id');
                })->keys()->toArray();
                $questionOptions = collect(json_decode($this->question->options));

                $unAnsweredOptions = $questionOptions->filter(function ($value, $k) use ($pluckedAnswerIds) {
                    if (!in_array($value->id, $pluckedAnswerIds)) {
                        return $value;
                    }
                });
                $ansCount = $answers->count();

                $answers = $answers->filter(function ($value, $k) {
                    if (!empty($value->answer)) {
                        return $value;
                    }
                });
                $groupedAnswer = $answers->groupBy(function ($item) {
                    return array_column(json_decode($item->answer), 'id');
                });
                $ans = [];
                foreach ($groupedAnswer as $key => $values) {
                    $arr = $selectedManualOptions = [];
                    $optionsData = collect(json_decode($this->question->options));

                    $selectedOption = $optionsData->filter(function ($value, $k) use ($key) {
                        if ($value->id === $key) {
                            return $value;
                        }
                    });

                    if ($selectedOption->isEmpty()) {

                        $values->map(function ($v, $k) use ($key, &$selectedManualOptions) {
                            $manualAnswerData = collect(json_decode($v->manual_answer));
                            $selectedManualOptions = $manualAnswerData->filter(function ($option) use ($key) {
                                return $option->id == $key;
                            })->values();
                        });
                        array_push($arr, $selectedManualOptions->implode('label', ', '));
                    } else {
                        array_push($arr, $selectedOption->implode('label', ', '));
                    }

                    array_push($arr, count($values));
                    array_push($arr, $ansCount);
                    $values->each(function ($item, $keyV) {
                        $workshop = Workshop::find($item->user_workshop_id);
                        $item->name = $workshop->workshop_name . " " . $item->user->fname . " " . $item->user->lname;
                    });

                    array_push($arr, $values->implode('name', PHP_EOL));
                    array_push($arr, number_format((count($values) / $ansCount) * 100, 2));

                    $values->each(function ($item, $itemK) use ($key) {
                        $itemAnswers = collect(json_decode($item->answer));
                        $item->choices = $itemAnswers->filter(function ($itemAnsValue, $itemAnsIndex) use ($key) {

                            if ($itemAnsValue->id == $key) {
                                return $itemAnsValue->comment_value;
                            }
                        });
                    });
                    $choices = $values->pluck('choices')->flatten()->implode('comment_value', ', ');
                    array_push($arr, $choices);
                    array_push($ans, $arr);
                }
                $unAns = collect([]);
                if ($unAnsweredOptions->count() > 0) {
                    $unAns = $unAnsweredOptions->map(function ($optValue, $optIndex) {
                        return [$optValue->label, '', '', '', '', ''];
                    });
                }
                $allAnswers = array_merge($ans, $unAns->toArray());
                $answers = collect($allAnswers);
//                    dd($answers, $this->question);
            }
            return $answers;
        }

        /**
         * @return string
         */
        public function title(): string
        {
            return (strlen(Unaccent($this->question->question)) > 20) ? substr(Unaccent($this->question->question), 0, 20) . '...' : Unaccent($this->question->question);
        }

        public function headings(): array
        {
            if (in_array($this->question->consultation_question_type_id, [15])) {
                $this->headingCellRange = 'A2:B2';
                $this->cellRange = 'A1:B1';
                return [__('message.topic_name'), __('message.respondants')];
            }
            if (in_array($this->question->consultation_question_type_id, [16])) {
                $this->headingCellRange = 'A2:D2';
                $this->cellRange = 'A1:D1';
                $options = collect(json_decode($this->question->options));
                $columns = collect($options['column'])->pluck('label');
                $columns->prepend(__('message.topic_name'));
                $columns->push(__('message.respondants'));
                return $columns->toArray();
            }
            if (in_array($this->question->consultation_question_type_id, [1, 6, 7, 12])) {
                $this->headingCellRange = 'A2:C2';
                $this->cellRange = 'A1:C1';
                return [__('message.answers'), __('message.respondants'), 'WORKSHOP'];
            }
            if (in_array($this->question->consultation_question_type_id, [2, 5, 9, 10, 11])) {
                $this->headingCellRange = 'A2:E2';
                $this->cellRange = 'A1:E1';
                return ['OPTIONS', __('message.nbr_of_occurence'), __('message.total_occurences'), __('message.percentage_of_occurences'), __('message.respondants')];
            }
            if (in_array($this->question->consultation_question_type_id, [3, 4])) {
                $this->headingCellRange = 'A2:C2';
                $this->cellRange = 'A1:C1';
                return [__('message.answers'), __('message.merged_answers'), __('message.respondants')];
            }
            if (in_array($this->question->consultation_question_type_id, [13, 14])) {
                $this->headingCellRange = 'A2:G2';
                $this->cellRange = 'A1:G1';
                return [__('message.topic_name'), __('message.nbr_of_citation'), __('message.total_nbr_of_respondants'), __('message.respondants'), __('message.percentage_of_citation'), __('message.slider_choice'), __('message.slider_average')];
            }
            if (in_array($this->question->consultation_question_type_id, [18])) {
                $this->headingCellRange = 'A2:H2';
                $this->cellRange = 'A1:H1';
                return [__('message.topic_name'), __('message.nbr_of_citation'), __('message.total_nbr_of_respondants'), __('message.respondants'), __('message.percentage_of_citation'), __('message.comment')];
            }
            $this->headingCellRange = 'A2:C2';
            return [__('message.answers'), __('message.merged_answers'), __('message.respondants')];

        }
    }