<nav id="pagination_<?php echo $this->get_paginateId() ?>" aria-label="Paginação">
    <ul class="pagination">
        
        <!--//laquo//-->
        <li class="<?php if($this->currentPageNumber == 1) echo "disabled"; ?>">
            
            <a href="#" aria-label="Página Anterior">
              <span aria-hidden="true">&laquo;</span>
            </a>
            
        </li>
        


<?php 

for($i=1; $i<=$this->maxPages; $i++) {
    $active = ($this->currentPageNumber == $i) ? 'active' : '';
    echo '<li class="'.$active.'"><a href="#">'.$i.'</a></li>';
}


?>    
    
    

    <li class="<?php if($this->currentPageNumber == $this->maxPages) echo "disabled"; ?>">
      <a href="#" aria-label="Próxima Página">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
  </ul>
</nav>      