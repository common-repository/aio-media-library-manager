jQuery(document).ready(function($) {
    
    
    var smack_media_folder_div = $(`
    
    <section>
   
    <nav id="sidebar" > <!-- Add an id to target the sidebar -->
        <header>
            <div class="imgBx">
            <div class="dem">    
                <h3>AioMedia</h3> 
               
                <button class="btn btn-success btn-sm create" id="smnew" style="cursor: pointer;">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M13 19C13 19.34 13.04 19.67 13.09 20H4C2.9 20 2 19.11 2 18V6C2 4.89 2.89 4 4 4H10L12 6H20C21.1 6 22 6.89 22 8V13.81C21.12 13.3 20.1 13 19 13C15.69 13 13 15.69 13 19M20 18V15H18V18H15V20H18V23H20V20H23V18H20Z"></path></svg>
                <span class="smtext">New Folder</span></button> <!-- New Folder button -->
                </div>
                <div id="aiomedialibrary" class="chan"> <!-- Move the aiomedialibrary div here -->        
                    <div class="demo1"><br>
                        <input type="text" class="searchsidebar" id="folder-search" placeholder="Enter Folder Name...">
                        <div class="mini">
                        <button class="btn btn-primary btn-sm rename" style="cursor: pointer;">
                        <svg viewBox="0 0 24 24" fill="currentColor"><path d="M19.39 10.74L11 19.13V20H4C2.9 20 2 19.11 2 18V6C2 4.89 2.89 4 4 4H10L12 6H20C21.1 6 22 6.89 22 8V10.15C21.74 10.06 21.46 10 21.17 10C20.5 10 19.87 10.26 19.39 10.74M13 19.96V22H15.04L21.17 15.88L19.13 13.83L13 19.96M22.85 13.47L21.53 12.15C21.33 11.95 21 11.95 20.81 12.15L19.83 13.13L21.87 15.17L22.85 14.19C23.05 14 23.05 13.67 22.85 13.47Z"></path></svg>
                        <span class="smtext">Rename</span></button> <!-- Rename button -->
                        <button class="btn btn-danger btn-sm delete" style="cursor: pointer;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="#2271B1" viewBox="0 0 110 110" width="20" height="20"><path fill="#2271b1" fill-rule="evenodd" d="M13 34c0-8.284 6.716-15 15-15h34.19a15 15 0 0 1 12.887 7.323l7.41 12.436a5 5 0 0 0 4.295 2.441H104c5.523 0 10 4.477 10 10V88c0 8.284-6.716 15-15 15H28c-8.284 0-15-6.716-15-15V34Zm66.701 36.8a5 5 0 0 1 7.072 0l4.792 4.791 4.446-4.446a5 5 0 0 1 7.071 7.071l-4.446 4.446 4.236 4.237a5 5 0 1 1-7.07 7.07l-4.237-4.236-4.446 4.446a5 5 0 1 1-7.07-7.071l4.444-4.446-4.791-4.792a5 5 0 0 1 0-7.07Z" clip-rule="evenodd" class="color000 svgShape"></path></svg>
                        <span class="smtext">Delete</span></button> <!-- Delete button -->
                                  
                       
                        <button class="btn btn-info btn-sm all" style="cursor: pointer;">
            
                        <svg id="SvgjsSvg1001"  xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:svgjs="http://svgjs.com/svgjs"><defs id="SvgjsDefs1002"></defs><g id="SvgjsG1008"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" ><path fill="#2271b1" d="M11,57V13H53V7H33L31,3H3V57a4,4,0,1,0,8,0Z" class="color78b9eb svgShape"></path><path fill="#2271b1" d="M44,23H28l-2,6H19V51H53V29H46ZM36,47a8,8,0,1,1,8-8A8,8,0,0,1,36,47Z" class="color78b9eb svgShape"></path><path fill="#1e81ce" d="M61,12H54V7a1,1,0,0,0-1-1H33.618L31.9,2.553A1,1,0,0,0,31,2H3A1,1,0,0,0,2,3V57a5.006,5.006,0,0,0,5,5H57a5.006,5.006,0,0,0,5-5V13A1,1,0,0,0,61,12ZM10,13V57a3,3,0,0,1-6,0V4H30.382L32.1,7.447A1,1,0,0,0,33,8H52v4H11A1,1,0,0,0,10,13ZM60,57a3,3,0,0,1-3,3H11a4.986,4.986,0,0,0,1-3V14H60Z" class="color1e81ce svgShape"></path><path fill="#1e81ce" d="M36,30a9,9,0,1,0,9,9A9.011,9.011,0,0,0,36,30Zm0,16a7,7,0,1,1,7-7A7.008,7.008,0,0,1,36,46Z" class="color1e81ce svgShape"></path><path fill="#1e81ce" d="M19,52H53a1,1,0,0,0,1-1V29a1,1,0,0,0-1-1H46.721l-1.773-5.316A1,1,0,0,0,44,22H28a1,1,0,0,0-.949.684L25.279,28H19a1,1,0,0,0-1,1V51A1,1,0,0,0,19,52Zm1-22h6a1,1,0,0,0,.949-.684L28.721,24H43.279l1.773,5.316A1,1,0,0,0,46,30h6V50H20Z" class="color1e81ce svgShape"></path></svg></g></svg>
                        <span class="smtext">All</span></button> <!-- All media button -->
                        <div id="uncate">
                        <button class="btn btn-info btn-sm unc" style="cursor: pointer;"><i class="fas fa-file-image"></i><span class="smtext">UnCategorized </span></button> <!-- uncategroized media button -->
                        </div>
                        </div>
                    </div>
                    <div class="demo2" >
                        <div class="row justify-content-center align-items-start">
                            <div id="jstree_demo_div" "></div> <!-- div to contain the jstree -->
                        </div>
                    </div>  
                </div>
            </div>
            <div class="toggle">
                <svg viewBox="0 0 24 24" fill="currentColor"><path d="M10.5 17a1 1 0 0 1-.71-.29 1 1 0 0 1 0-1.42L13.1 12 9.92 8.69a1 1 0 0 1 0-1.41 1 1 0 0 1 1.42 0l3.86 4a1 1 0 0 1 0 1.4l-4 4a1 1 0 0 1-.7.32z"></path></svg>
            </div>
        </header>
    </nav>
</section>




`);

$("#wpbody").prepend(smack_media_folder_div);
$("#wpbody").addClass("smack_library");

 // Add event listeners for toggle, search icon, and dark mode switch
 const activeToggle = document.querySelector('.toggle');
 const nav = document.querySelector('nav');

 activeToggle.addEventListener('click', () => {
     nav.classList.toggle('active');
 });

 $(".toggle").click(function () {
    $("#sidebar").toggleClass("active");
    nav.classList.toggle("active");
    $(".mini").toggleClass("expanded");
    // $("#smnew").toggleClass("expanded");
    // $("button.btn.btn-success.btn-sm.create").toggleClass("expanded");
    $("#jstree_demo_div").toggleClass("expanded");
  });

  $("#sidebar").resizable({
    handles: "ew",
    minWidth: 325,
    maxWidth: 530,
    resize: function (event, ui) {
      var sidebarWidth = ui.size.width;
      var searchInputWidth = sidebarWidth - 70;
      $(".searchsidebar").css("width", searchInputWidth + "px");
      $(".mini").css("width", searchInputWidth + "px");
      $("#jstree_demo_div").css("width", sidebarWidth + "px");

    },
  });
    function saveFolderToDatabase(newName,parentId) {
        var nonce = folder_script.nonce;
        var data = {
            action: 'AiomlSmack_save_folder_to_database',
            folder_name: newName,
            parent_id: parentId,
            security: nonce
        };

        $.post(folder_script.ajax_url, data, function(response) {
            console.log('Folder saved to database:', newName);
            location.reload();
            

        });
    }

    // function editFolder() {
    //     var currentName = $(this).closest('li').find('.folder-name').text();
    //     var newName = prompt("Enter the new folder name:", currentName);
    //     if (newName !== null && newName.trim() !== '') {
    //         $(this).closest('li').find('.folder-name').text(newName);
    //         var oldName = currentName;
    //         updateFolderInDatabase(oldName, newName);
    //     }
    // }

    function updateFolderInDatabase(oldName, newName) {
        var nonce = folder_script.nonce;   
        var data = {
            action: 'AiomlSmackupdate_folder_in_database',
            folder_name: oldName,
            new_name: newName,
            security: nonce
        };

        $.post(folder_script.ajax_url, data, function(response) {
            // console.log('Folder updated in database:', newName);
            window.location.reload();
        });
    }

    


    //on click media render
    $('#jstree_demo_div').on('select_node.jstree', function (e, data) {
        var selectedNode = data.node; 
        var folderSlug = selectedNode.original.slug;
        
        $('.attachment-filters').val(folderSlug).trigger('change');
    
    //trigger filter button
        // $('#post-query-submit').click();
    });
    
    
   
 
    
    $('.all').on('click', function() {
      
          $('.attachment-filters').val('all');
          $('.attachment-filters').trigger('change');
          $('#post-query-submit').click();
        
        //   location.reload();
      });
      $('.unc').on('click', function() {
      
        $('.attachment-filters').val('uncategorized');
        $('.attachment-filters').trigger('change');
        $('#post-query-submit').click();
      
      //   location.reload();
    });
  
     //  click event listener to the rename button
$('.rename').click(function() {
    // Get the reference to the jstree instance
    var tree = $('#jstree_demo_div').jstree(true);
    
    // Get the ID of the currently selected node
    var selectedNode = tree.get_selected();
    
    // If there is no selected node, return
    if (selectedNode.length === 0) {
        return;
    }
    
    // Start inline editing for the selected node
    tree.edit(selectedNode);
});

// Attach an event listener for the 'rename_node' event
$('#jstree_demo_div').on('rename_node.jstree', function(event, data) {
    // Get the old and new names of the node
    var oldName = data.old;
    var newName = data.text;
    
   
    updateFolderInDatabase(oldName, newName);
});



$('#smnew').click(function() {
    var tree = $('#jstree_demo_div').jstree(true);
    var selectedNode = tree.get_selected();

    // Create a new folder
    var newNode = tree.create_node(selectedNode.length === 0 ? null : selectedNode[0], {
        text: '', 
        type: 'input', 
      
    });

    // Open the newly created folder for editing
    tree.edit(newNode);
    $('#jstree_demo_div').on("rename_node.jstree", function (e, data) {
        var newNode = data.node;
        var parentId = data.node.parent;
        var newName = data.text;
        saveFolderToDatabase(newName, parentId);
        
    });
  
});
    


  
    

    $('.delete').click(function() {
       
        var tree = $('#jstree_demo_div').jstree(true);
        
        // Get the ID of the currently selected node
        var selectedNode = tree.get_selected();
        seletedId = selectedNode[0];
        var node = tree.get_node(seletedId);
        var nodeName = node.text;
      
        // If there is no selected node, return
        if (selectedNode.length === 0) {
            return;
        }
        var confirmDelete = confirm("Are you sure you want to delete this Folder?");
        
        // Delete the selected node from the tree
        if (confirmDelete) {
            // Delete the selected node from the tree
            tree.delete_node(selectedNode);
            deleteFolderFromDatabase(nodeName);
        }
    });

    
    function fetchFoldersFromDatabase() {
        var data = {
            action: 'AiomlSmack_fetch_folders_from_database'
        };
    
        $.post(folder_script.ajax_url, data, function(response) {
            var jsonResponse = JSON.parse(response);
    
            // Create nodes without considering parent-child relationships
            var nodesMap = {};
            jsonResponse.forEach(function(folder) {
                var node = {
                    id: folder.term_id,
                    text: folder.name,
                    children: [],
                    slug: folder.slug
                };
                nodesMap[node.id] = node;
            });
    
            // Establish parent-child relationships
            jsonResponse.forEach(function(folder) {
                var node = nodesMap[folder.term_id];
                if (folder.parent != "0") {
                    // If parent value is higher, find the correct parent node by traversing
                    var parentTermId = parseInt(folder.parent);
                    var parentNode = findParentNode(nodesMap, parentTermId);
                    if (parentNode) {
                        parentNode.children.push(node);
                    }
                }
            });
    
            // Find and set root nodes
            var rootNodes = [];
            for (var nodeId in nodesMap) {
                var node = nodesMap[nodeId];
                if (!hasParent(nodesMap, node.id)) {
                    rootNodes.push(node);
                }
            }
    
            // Initialize jsTree with the structured data
            $('#jstree_demo_div').jstree({
                'core': {
                    'data': rootNodes,
                    "check_callback": true,
                    "dblclick_toggle": false // Disable default double click event
                },
                "plugins": ["dnd", "contextmenu", "wholerow", "search"] // dnd plugin for search
            });
        });
    }
    
    // Function to find parent node by term_id
    function findParentNode(nodesMap, termId) {
        if (!nodesMap.hasOwnProperty(termId)) {
            return null;
        }
        return nodesMap[termId];
    }
    
    // Function to check if a node has a parent
    function hasParent(nodesMap, termId) {
        for (var nodeId in nodesMap) {
            var node = nodesMap[nodeId];
            if (node.children.some(child => child.id == termId)) {
                return true;
            }
        }
        return false;
    }
    
    fetchFoldersFromDatabase();
    
    
        //folder drag and drop
        $('#jstree_demo_div').on('move_node.jstree', function (e, data) {
            var draggedNodeId = data.node.id; //  term_id of the dragged node
            var targetNodeId = data.parent; //  term_id of the target folder where the node was dropped
        //move folder as a root or separate folder
            if (targetNodeId === "#") {
                // console.log("Node was dropped at the root level of the tree.");
               data.parent=0;
               targetNodeId=data.parent;
               folderDragDrop(draggedNodeId,targetNodeId);
            } else {
                console.log("Dragged Node ID:", draggedNodeId);
                console.log("Target Node ID:", targetNodeId);
               folderDragDrop(draggedNodeId,targetNodeId);
            }
        });


//to backend
        function folderDragDrop(draggedNodeId,targetNodeId){
            var nonce = folder_script.nonce;  
            var data = {
                action: 'AiomlSmackfolder_DragDrop_database',
                dragged: draggedNodeId,
                target: targetNodeId,
                security: nonce 
            };
    
            $.post(folder_script.ajax_url, data, function(response) {
                // console.log(response);
                location.reload();
            });
        }

  

    // Enable inline editing when a node is double-clicked 
    $('#jstree_demo_div').on('dblclick', '.jstree-anchor', function() {
        var node = $('#jstree_demo_div').jstree(true).get_node($(this));
        
        // Disable editing for root nodes
        if (!node.parent) {
            return;
        }
        
        // Start inline editing for the node
        $('#jstree_demo_div').jstree(true).edit(node);
    });

    // Modify the event listener for the search input field
    $('#folder-search').keyup(function(event) {
        if (event.key === "Enter") {
            var searchString = $(this).val();
            $('#jstree_demo_div').jstree(true).search(searchString);
        }
    });

    
    

    function deleteFolderFromDatabase(folderName) {
        var nonce = folder_script.nonce;
        var data = {
            action: 'AiomlSmack_delete_folder_from_database',
            folder_name: folderName,
            security: nonce 
        };
        $.post(folder_script.ajax_url, data, function(response) {
            // console.log('Folder deleted from database:', folderName);
            location.reload();
        });
    }


   
    // fetchFoldersFromDatabase();
    var drappable = {
        // accept: ".draggable",
        drop: function (event, ui) {
        //   console.log("event", event);
          // console.log("droppable",ui.draggable,ui.draggable.data('id'));
          var droppable = $(this);
        //   console.log(ui);
          var draggable = ui.draggable;
          var dataId = draggable.data("id");
        //   console.log(dataId);
          // Move draggable into droppable
          // draggable.clone().appendTo(droppable);
          var folderName = droppable.data("folder-name");
          var folderId = droppable.data("folder-id");
          var tree = $("#jstree_demo_div").jstree(true);
          // console.log("tree",tree,"jjhjh",droppable);
          var droppableNodeID = droppable.attr("id");
          // console.log(" droppableNodeID", droppableNodeID);
          var droppableNode = tree.get_node(droppableNodeID);
        //   console.log(folderId);
          if (
            droppableNode &&
            droppableNode.original &&
            droppableNode.original.slug
          ) {
            var folderSlug = droppableNode.original.slug;
          } else {
            console.log("Folder slug not found");
          }
          // Move draggable into droppable
          // draggable.clone().appendTo(droppable);
    
          var folderName = droppable.data("folder-name");
    

          Toastify({
            text: "Successfully moved",
            duration: 5000, // 5 seconds
            close: true, // Enable close button
            gravity: "top", // Top position
            position: "right", // Right side
            backgroundColor: "linear-gradient(to right, #21759b, #33aaff)", // Background color
            stopOnFocus: true, // Stop timeout on focus
        }).showToast();
        
         
    
          // saveMediaToFolder(dataId, folderId);
          moveAttachmentsToCategory(dataId, folderSlug);
          //   alert(folderName);
          //draggable.css({top: '5px', left: '5px'});
        },
      };
      //drag
      setTimeout(() => {
        // Function to initialize draggable functionality
        function initializeDraggable() {
          $(".attachments li").draggable({
            helper: "clone", // Create a clone of the original element
            cursor: "move", // Set the cursor to move
            revert: "invalid", // If not dropped, the item will revert back to its original position
            cursorAt: { top: 20, left: 20 },
            helper: function (event) {
              var selectedImages = $(".attachment.selected");
              if (selectedImages.length > 0) {
                // Clone selected images
                selectedImages = selectedImages.clone();
              } else {
                // No selected images, clone the original element
                selectedImages = $(this).clone();
              }
            //   console.log("selectedfImages", selectedImages);
              return $('<div class="ui-widget-header">Move Items</div>').append(
                selectedImages
              );
            },
    
         
          });
        }
       
    
        // Function to handle changes to the .attachments element and its child nodes
        function handleAttachmentsChanges(mutationsList, observer) {
          mutationsList.forEach((mutation) => {
            // Check if nodes were added
            if (mutation.type === "childList" && mutation.addedNodes.length > 0) {
              // Apply draggable functionality to added nodes
              initializeDraggable();
            }
          });
        }
    
        // Create a new MutationObserver instance
        const attachmentsObserver = new MutationObserver(handleAttachmentsChanges);
    
        // Observe changes to the .attachments element and its subtree
        attachmentsObserver.observe($(".attachments")[0], {
          childList: true,
          subtree: true,
        });
    
        // Initial application of draggable functionality to existing list items
        initializeDraggable();
    
        $(".jstree-container-ul li").droppable(drappable);
        $(".jstree-children li").droppable(drappable);
        // console.log($(".jstree-children li"));
      }, 2000);
    
      $("#aiomedialibrary").on("DOMSubtreeModified", function () {
        setTimeout(() => {
          $(".jstree-container-ul li").droppable(drappable);
        }, 1000);
      });
      // $(".sf_tree_droppable li span").on("DOMSubtreeModified", function () {
      //   $(".attachment").draggable(dragOpts);
      //   $(".sf_tree_droppable li").droppable(dropOption);
      // });
    
// Make the "UnCategorized" button droppable
$('.unc').droppable({
    drop: function(event, ui) {
        var draggable = ui.draggable;
        var dataId = draggable.data('id');
        var folderSlug = 'uncategorized'; // Set folder slug as "uncategorized"
        
        // Move the attachments to the "uncategorized" category
        moveAttachmentsToCategory(dataId, folderSlug);
        
        // Show success message
        Toastify({
            text: "Successfully moved To uncategorized ",
            duration: 5000, // 5 seconds
            close: true, // Enable close button
            gravity: "top", // Top position
            position: "right", // Right side
            backgroundColor: "linear-gradient(to right, #21759b, #33aaff)", // Background color
            stopOnFocus: true, // Stop timeout on focus
        }).showToast();
    }
});
$("#aiomedialibrary").on("DOMSubtreeModified", function () {
    setTimeout(() => {
      $(".jstree-container-ul li").droppable(drappable);
    }, 1000);
  });
function moveAttachmentsToCategory(dataId, folderSlug) {
    var nonce = folder_script.nonce;
    var data = {
        action: 'AiomlSmack_move_attachments_to_category',
        attachment_id: dataId, 
        folder_name: folderSlug,
        security: nonce  

    };

    $.post(folder_script.ajax_url, data, function(response) {
        // console.log(response);
        // alert("Successfully Moved To the Selected Folder")
        
    });
}







});
