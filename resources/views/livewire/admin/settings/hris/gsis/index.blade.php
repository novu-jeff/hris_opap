<div>
    
    <div class="modal fade" wire:ignore.self id="uploadBilling" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5 text-uppercase fw-bold" id="staticBackdropLabel">Upload GSIS Billing</h1>
                    <button type="button" class="btn-close" wire:click="close_upload" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4 px-4">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <label class="mb-2" for="file">File Upload</label>
                            <input type="file" wire:model="file" id="file" class="form-control">
                            <div class="mt-2 text-muted fw-bold text-uppercase d-flex justify-content-between align-items-center" style="font-size: 13px">
                                <small>Note: only files xlsx or xls are allowed.</small>
                                <small><a href="{{asset('templates/HRIS TEMPLATE.xlsx')}}" class="nav-link text-decoration-underline">Download Template</a></small>
                            </div>
                            <div wire:loading wire:target="file" class="mt-2 text-center text-muted">
                                <p>Please Wait... <i class="fa-solid fa-spinner fa-spin"></i></p>
                            </div>
                            @error('file') 
                                <span class="text-danger">{{ $message }}</span> 
                            @enderror
                            <div class="mt-3">
                                @if($upload_preview)
                                    File Ready to import: <a href="{{$upload_preview}}">{{$upload_preview}}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 d-flex justify-content-end">
                        @if($upload_preview)
                            <button class="btn btn-primary px-5 py-3 text-uppercase fw-bold" 
                                    wire:click="upload_file"
                                    wire:loading.attr="disabled">
                                <span wire:loading.remove>Upload File</span>
                                <span wire:loading>Importing <i class="fa-solid fa-spinner fa-spin"></i></span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-end mb-5 gap-3">
        @if(empty($items))
            <button class="btn btn-primary px-5 py-3 text-uppercase" wire:click="uploadRecords">Upload Billing</button>
        @else
            <a href="{{route('gsis.index')}}" class="btn btn-outline-primary px-5 py-3 text-uppercase">Go Back</a>
        @endif
    </div>

    @if(empty($items))
        <div class="table-responsive" wire:ignore>
            <table class="table data-tables w-100">
                <thead>
                    <tr>
                        <th>Billing Month</th>
                        <th>Remitting Agency</th>
                        <th>Office Code</th>
                        <th>Total Records</th>
                        <th style="max-width: 200px;">Action</th>
                    </tr>
                </thead>                
                <tbody>
                    @foreach($records as $record)
                        <tr data-id="{{$record->id}}">
                            <td>{{$record->billing_month}}</td>
                            <td>{{$record->remitting_agency}}</td>
                            <td>{{$record->office_code}}</td>
                            <td>{{$record->items->count() ?? 0 }} Employees</td>
                            <td>
                                <button wire:click="show({{$record->id}})" class="btn btn-primary mx-1">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button wire:click="remove(true, {{$record->id}})" class="btn btn-danger mx-1">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <ul class="list-unstyled">
            <li class="mb-2 text-uppercase">
                Remitting Agency: <span class="text-decoration-underline fw-bold">{{$items->remitting_agency}}</span>
            </li>
            <li class="mb-2 text-uppercase">
                Office Code: <span class="text-decoration-underline fw-bold">{{$items->office_code}}</span>
            </li>
            <li class="mb-2 text-uppercase">
                Due Month: <span class="text-decoration-underline fw-bold">{{$items->billing_month}}</span>
            </li>
        </ul>
        <hr class="mt-4">
        <div class="table-responsive mt-4">
            <table class="table data-tables w-100">
                <thead>
                    <tr>
                        <th>BP No</th>
                        <th>CRN No</th>
                        <th>Effectivity Date</th>
                        <th>PS</th>
                        <th>GS</th>
                        <th>EC</th>
                        <th>Consoloan</th>
                        <th>Ecardplus</th>
                        <th>Salary Loan</th>
                        <th>Cash Adv</th>
                        <th>Emergency Loan</th>
                        <th>Education Loan</th>
                        <th>ELA</th>
                        <th>SOS</th>
                        <th>PLREG</th>
                        <th>Plopt</th>
                        <th>REL</th>
                        <th>LCH DCS</th>
                        <th>Stock Purchase</th>
                        <th>Opt Life</th>
                        <th>CEAP</th>
                        <th>Edu Child</th>
                        <th>Genesis</th>
                        <th>GenPlus</th>
                        <th>GenFlexi</th>
                        <th>GenSpcl</th>
                        <th>Help</th>
                        <th>GFAL</th>
                        <th>MPL</th>
                        <th>CPL</th>
                        <th>GEL</th>
                    </tr>
                </thead>                
                <tbody>
                    @foreach($items->items as $item)
                        <tr data-id="{{$item->id}}">
                            <td style="width: 300px !important">{{$item->bp_no}}</td>
                            <td style="width: 300px !important">{{$item->crn_no}}</td>
                            <td style="width: 300px !important">{{$item->effectivity_date}}</td>
                            <td style="width: 300px !important">{{$item->ps}}</td>
                            <td style="width: 300px !important">{{$item->gs}}</td>
                            <td style="width: 300px !important">{{$item->ec}}</td>
                            <td style="width: 300px !important">{{$item->consoloan}}</td>
                            <td style="width: 300px !important">{{$item->ecardplus}}</td>
                            <td style="width: 300px !important">{{$item->salary_loan}}</td>
                            <td style="width: 300px !important">{{$item->cash_adv}}</td>
                            <td style="width: 300px !important">{{$item->emrgy_loan}}</td>
                            <td style="width: 300px !important">{{$item->educ_loan}}</td>
                            <td style="width: 300px !important">{{$item->ela}}</td>
                            <td style="width: 300px !important">{{$item->sos}}</td>
                            <td style="width: 300px !important">{{$item->plreg}}</td>
                            <td style="width: 300px !important">{{$item->plopt}}</td>
                            <td style="width: 300px !important">{{$item->rel}}</td>
                            <td style="width: 300px !important">{{$item->lch_dcs}}</td>
                            <td style="width: 300px !important">{{$item->stock_purchase}}</td>
                            <td style="width: 300px !important">{{$item->opt_life}}</td>
                            <td style="width: 300px !important">{{$item->ceap}}</td>
                            <td style="width: 300px !important">{{$item->edu_child}}</td>
                            <td style="width: 300px !important">{{$item->genesis}}</td>
                            <td style="width: 300px !important">{{$item->genplus}}</td>
                            <td style="width: 300px !important">{{$item->genflexi}}</td>
                            <td style="width: 300px !important">{{$item->genspcl}}</td>
                            <td style="width: 300px !important">{{$item->help}}</td>
                            <td style="width: 300px !important">{{$item->gfal}}</td>
                            <td style="width: 300px !important">{{$item->mpl}}</td>
                            <td style="width: 300px !important">{{$item->cpl}}</td>
                            <td style="width: 300px !important">{{$item->gel}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
