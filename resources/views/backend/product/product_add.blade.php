@extends('admin.admin_master')
@section('admin')

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

<div class="page-wrapper">
    <div class="page-content">

        <!--breadcrumb-->
        <div class="page-breadcrumb d-none d-sm-flex align-items-center mb-3">
            <div class="breadcrumb-title pe-3">Add New Product</div>
            <div class="ps-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 p-0">
                        <li class="breadcrumb-item"><a href="javascript:;"><i class="bx bx-home-alt"></i></a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">New Product</li>
                    </ol>
                </nav>
            </div>

        </div>
        <!--end breadcrumb-->

        <div class="card">
            <div class="card-body p-4">
                <h5 class="card-title">Add New Product</h5>
                <hr>
                <div class="form-body mt-4">

                <!-- FORM -->
                <form method="post" action="{{ route('upload.product') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-lg-8">
                    <div class="border border-3 p-4 rounded">
                        <div class="mb-3">
                            <label for="inputProductTitle" class="form-label">Product Title</label>
                            <input type="text" name="title" class="form-control" id="inputProductTitle" placeholder="Enter product title (Max 6 Characters Are Best)">
                        </div>

                        <div class="mb-3">
                            <label for="inputProductTitle" class="form-label">Product Code (Must Be Unique)</label>
                            <input type="text" name="product_code" class="form-control" id="inputProductTitle" placeholder="Enter product code">
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Product Thumbnail</label>
                            <input class="form-control" type="file" 
                            id="image"
                            name="image">
                        </div>

                        <div class="mb-3">
                            <img id="showImage" src="{{ url('upload/no_image.jpg') }}" style="width:100px; height: 100px;"/>
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Image One</label>
                            <input class="form-control" type="file" 
                            name="image_one">
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Image Two</label>
                            <input class="form-control" type="file" 
                            name="image_two">
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Image Three</label>
                            <input class="form-control" type="file" 
                            name="image_three">
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Image Four</label>
                            <input class="form-control" type="file" 
                            name="image_four">
                        </div>

                        <div class="mb-3">
                            <label for="inputProductDescription" class="form-label">Short Description</label>
                            <textarea name="short_description" class="form-control" id="inputProductDescription" rows="3"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="inputProductDescription" class="form-label">Long Description</label>
                        
							<textarea id="mytextarea" name="long_description">
                    
                            </textarea>
                        </div>
                    </div>
                    </div>


                    <!-- --------------------------- -->

                    <div class="col-lg-4">
                    <div class="border border-3 p-4 rounded">

                        <div class="row g-3">
                        <div class="form-check form-switch">
                            <input name="soldout" class="form-check-input" type="checkbox" id="soldout">
                            <label class="form-label" for="flexSwitchCheckChecked">SOLD OUT</label>
                        </div>
                        <div class="col-md-6">
                            <label for="inputPrice" class="form-label">Price</label>
                            <input type="text" name="price" class="form-control" id="inputPrice" placeholder="00.00">
                        </div>
                        <div class="col-md-6">
                            <label for="inputCompareatprice" class="form-label">Special Price</label>
                            <input type="text" name="special_price" class="form-control" id="inputCompareatprice" placeholder="00.00">
                        </div>

                       
                        <div class="col-12">
                            <label for="inputProductType" class="form-label">Product Category</label>
                            <select name="category" class="form-select" id="category">
                                <option selected="">Select Category</option>
                                @foreach($category as $item)
                                <option value="{{ $item->category_name }}">{{ $item->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label for="inputVendor" class="form-label">Product Subcategory</label>
                            <select name="subcategory" class="form-select" id="subcategory">
                                <option selected="">Select Subcategory</option>
                                <!-- @foreach($subcategory as $item)
                                <option value="{{ $item->subcategory_name }}">{{ $item->subcategory_name }}</option>
                                @endforeach -->
                            </select>
                        </div>


                        <div class="col-12">
                            <label class="form-label">Brand</label>
                            <input type="text" name="brand" class="form-control" placeholder="Input Brand">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Size</label>
                            <input type="text" name="size" class="form-control visually-hidden" data-role="tagsinput" 
                                value="XS, S, M, L, XL">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Color</label>
                            <input type="text" name="color" class="form-control visually-hidden" data-role="tagsinput" 
                                value="red,white,black">
                        </div>

                        <div class="form-check">
                            <input name="remark" class="form-check-input" type="radio" value="BEST SELLER" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">BEST SELLER</label>
                        </div>

                        <div class="form-check">
                            <input name="remark" class="form-check-input" type="radio" value="NEW" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">NEW</label>
                        </div>

                        <div class="form-check">
                            <input name="remark" class="form-check-input" type="radio" value="SALE" id="flexCheckDefault">
                            <label class="form-check-label" for="flexCheckDefault">SALE</label>
                        </div>

                        <div class="col-12">
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Save Product</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div><!--end row-->
        </form>
    </div>
            </div>
        </div>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function() {
        $('#image').change(function(e) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#showImage').attr('src', e.target.result);
            }
            reader.readAsDataURL(e.target.files['0'])
        })

        $('#soldout').on('change', function() {
            if($('#soldout')[0].checked) {
                $('#soldout')[0].value = 1;
            } else {
                $('#soldout')[0].value = 0;
            }
        })

        // update subcategory when category is select
        var selecCategory = '';
        // Jquery Pass PHP Laravel 5.6 Variables To Javascrip
        var subcategory = {!! json_encode($subcategory->toArray()) !!};
        var filterSubcategory;

        $('#category').on('change', function() {
            selecCategory = $('#category')[0].value;
            filterSubcategory = subcategory.filter(item => item['category_name'] == selecCategory);
            
            // remove all previous option
            $('#subcategory').empty();

            // add default option
            $('#subcategory').append($("<option></option>").text("Select Subcategory"));

            // add option
            $.each(filterSubcategory, function(key, item) {
                $('#subcategory')
                    .append($("<option></option>")
                        .attr("value", item.subcategory_name)
                        .text(item.subcategory_name));
            })
        })        
    })
    
</script>



<!-- For Long Description -->
<script src='https://cdn.tiny.cloud/1/vdqx2klew412up5bcbpwivg1th6nrh3murc6maz8bukgos4v/tinymce/5/tinymce.min.js' referrerpolicy="origin">
</script>
<script>
    tinymce.init({
        selector: '#mytextarea'
    });
</script>


@endsection